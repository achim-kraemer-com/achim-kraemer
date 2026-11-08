<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PublicUrlProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Meldet geänderte Seiten per IndexNow an Bing, Yandex, Seznam, Naver & Co.
 *
 * @see https://www.indexnow.org/documentation
 */
#[AsCommand(name: 'app:indexnow:submit', description: 'Meldet URLs per IndexNow an die Suchmaschinen')]
class IndexNowSubmitCommand extends Command
{
    private const ENDPOINT = 'https://api.indexnow.org/indexnow';
    private const MAX_URLS = 10000;

    public function __construct(
        private readonly PublicUrlProvider $urlProvider,
        #[Autowire('%app.indexnow_key%')] private readonly string $key,
        #[Autowire('%app.site_url%')] private readonly string $siteUrl,
        #[Autowire('%kernel.project_dir%/public')] private readonly string $publicDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('urls', InputArgument::IS_ARRAY, 'Einzelne URLs; ohne Angabe werden alle Seiten aus der Sitemap gemeldet')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Nur anzeigen, nichts senden');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $host = (string) \parse_url($this->siteUrl, \PHP_URL_HOST);

        if (!\preg_match('/^[a-zA-Z0-9-]{8,128}$/', $this->key)) {
            $io->error('Ungültiger IndexNow-Schlüssel (8-128 Zeichen a-z, A-Z, 0-9, -).');

            return Command::FAILURE;
        }

        $keyFile = $this->publicDir.'/'.$this->key.'.txt';
        if (!\is_file($keyFile) || \trim((string) \file_get_contents($keyFile)) !== $this->key) {
            $io->error(\sprintf('Key-Datei %s fehlt oder enthält nicht den Schlüssel.', $keyFile));

            return Command::FAILURE;
        }

        $urls = $input->getArgument('urls') ?: $this->urlProvider->getUrls();
        $urls = \array_values(\array_filter($urls, static fn (string $url): bool => \parse_url($url, \PHP_URL_HOST) === $host));

        if ([] === $urls) {
            $io->warning(\sprintf('Keine URLs für %s gefunden.', $host));

            return Command::SUCCESS;
        }

        $io->listing($urls);

        if ($input->getOption('dry-run')) {
            $io->note(\sprintf('Dry-Run: %d URL(s) würden gemeldet.', \count($urls)));

            return Command::SUCCESS;
        }

        foreach (\array_chunk($urls, self::MAX_URLS) as $chunk) {
            [$status, $body] = $this->post([
                'host'        => $host,
                'key'         => $this->key,
                'keyLocation' => \rtrim($this->siteUrl, '/').'/'.$this->key.'.txt',
                'urlList'     => $chunk,
            ]);

            if (200 !== $status && 202 !== $status) {
                $io->error(\sprintf('IndexNow antwortete mit HTTP %d: %s %s', $status, $this->explain($status), $body));

                return Command::FAILURE;
            }

            $io->success(\sprintf('%d URL(s) gemeldet (HTTP %d).', \count($chunk), $status));
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{int, string}
     */
    private function post(array $payload): array
    {
        $context = \stream_context_create(['http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json; charset=utf-8\r\n",
            'content'       => \json_encode($payload, \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR),
            'timeout'       => 15,
            'ignore_errors' => true,
        ]]);

        $body   = @\file_get_contents(self::ENDPOINT, false, $context);
        $status = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (\preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $m)) {
                $status = (int) $m[1];
            }
        }

        return [$status, \trim((string) $body)];
    }

    private function explain(int $status): string
    {
        return match ($status) {
            0       => 'Keine Verbindung zum IndexNow-Endpunkt.',
            400     => 'Ungültiges Format.',
            403     => 'Schlüssel nicht gültig (Key-Datei nicht erreichbar oder falsch).',
            422     => 'URLs gehören nicht zum Host oder Schlüssel passt nicht zum Schema.',
            429     => 'Zu viele Anfragen (möglicher Spam).',
            default => '',
        };
    }
}

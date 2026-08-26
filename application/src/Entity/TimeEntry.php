<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    final public const STATUS_OPEN    = 'offen';
    final public const STATUS_IN_WORK = 'in Bearbeitung';
    final public const STATUS_PAYED   = 'bezahlt';
    final public const STATUS_TYPES   = [
        'Offen'          => self::STATUS_OPEN,
        'in bearbeitung' => self::STATUS_IN_WORK,
        'Bezahlt'        => self::STATUS_PAYED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $hours = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $invoiced = false;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\ManyToMany(targetEntity: Invoice::class, mappedBy: 'timeEntry')]
    private Collection $invoices;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_OPEN;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $price = null;

    /**
     * Timestamp when the currently running timer was started; null when the timer is stopped.
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $timerStartedAt = null;

    /**
     * Total measured seconds from all completed start/stop intervals.
     */
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $accumulatedSeconds = 0;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(?string $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isInvoiced(): ?bool
    {
        return $this->invoiced;
    }

    public function setInvoiced(bool $invoiced): static
    {
        $this->invoiced = $invoiced;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->addTimeEntry($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            $invoice->removeTimeEntry($this);
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTimerStartedAt(): ?\DateTimeImmutable
    {
        return $this->timerStartedAt;
    }

    public function setTimerStartedAt(?\DateTimeImmutable $timerStartedAt): static
    {
        $this->timerStartedAt = $timerStartedAt;

        return $this;
    }

    public function getAccumulatedSeconds(): int
    {
        return $this->accumulatedSeconds;
    }

    public function setAccumulatedSeconds(int $accumulatedSeconds): static
    {
        $this->accumulatedSeconds = $accumulatedSeconds;

        return $this;
    }

    public function isTimerRunning(): bool
    {
        return $this->timerStartedAt !== null;
    }

    /**
     * Total elapsed seconds including the currently running interval, if any.
     */
    public function getElapsedSeconds(): int
    {
        $elapsed = $this->accumulatedSeconds;

        if ($this->timerStartedAt !== null) {
            $elapsed += \max(0, (new \DateTimeImmutable())->getTimestamp() - $this->timerStartedAt->getTimestamp());
        }

        return $elapsed;
    }

    /**
     * Starts the timer if it is not already running.
     */
    public function startTimer(): static
    {
        if ($this->timerStartedAt === null) {
            $this->timerStartedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Stops the timer and folds the running interval into the accumulated seconds.
     */
    public function stopTimer(): static
    {
        if ($this->timerStartedAt !== null) {
            $this->accumulatedSeconds += \max(0, (new \DateTimeImmutable())->getTimestamp() - $this->timerStartedAt->getTimestamp());
            $this->timerStartedAt = null;
        }

        return $this;
    }
}

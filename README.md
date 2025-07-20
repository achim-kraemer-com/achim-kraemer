# Achim-Kraemer.com Website

This is the repository for the personal website and project portfolio of Achim Kraemer, built with Symfony 7.

## Table of Contents

- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [Usage](#usage)
  - [Development](#development)
  - [Production](#production)
- [Built With](#built-with)
- [License](#license)
- [Contact](#contact)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- Docker

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/achim-kraemer.com.git
   cd achim-kraemer.com
   ```

2. **Install PHP dependencies:**
   ```bash
   cd application
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

4. **Set up environment variables:**
   - Create a `.env.local` file in the `application` directory by copying the `.env` file.
   - Update the variables in `.env.local` with your local database credentials and other settings.

5. **Start the Docker containers:**
   ```bash
   docker-compose up -d
   ```

6. **Run database migrations:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

## Usage

### Development

To run the development server with hot-reloading, use the following command:

```bash
npm run watch
```

This will watch for changes in your assets and automatically rebuild them.

### Production

To build the assets for production, use the following command:

```bash
npm run build
```

## Built With

* [Symfony](https://symfony.com/) - The web framework used
* [Doctrine](https://www.doctrine-project.org/) - ORM for database management
* [Twig](https://twig.symfony.com/) - The template engine for PHP
* [Webpack Encore](https://symfony.com/doc/current/frontend.html) - For asset management
* [Tailwind CSS](https://tailwindcss.com/) - For styling
* [Stimulus](https://stimulus.hotwired.dev/) - JavaScript framework
* [Docker](https://www.docker.com/) - For containerization

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Contact

Achim Kraemer - [kontakt@achim-kraemer.com](mailto:your-email@example.com)

Project Link: [https://github.com/achim-kraemer-com/achim-kraemer](https://github.com/achim-kraemer-com/achim-kraemer)
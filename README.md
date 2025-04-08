# Electron PHP Adminer

**⚠️ DEPRECATED: This project is no longer maintained due to security concerns. Running uncontrolled server-side applications within an Electron wrapper makes it difficult to properly secure and maintain.**

An Electron wrapper for [Adminer](https://www.adminer.org/), a lightweight database management tool.

## Features

- Full-featured database management through Adminer
- Works offline
- Cross-platform desktop application (though currently best tested on macOS)
- Embedded PHP server

## System Requirements

- **macOS**: PHP must be installed or included in the application package
- **Windows/Linux**: Limited testing, may require additional configuration

## How It Works

This application:
1. Starts a PHP server on a random port between 5000-6000
2. Serves the Adminer PHP application through this local server
3. Displays the interface in an Electron window

## Screenshots
![Login Screen](https://github.com/iketiunn/electron-php-adminer/blob/main/screenshots/0.png?raw=true)
![Database View](https://github.com/iketiunn/electron-php-adminer/blob/main/screenshots/1.png?raw=true)
![Table Structure](https://github.com/iketiunn/electron-php-adminer/blob/main/screenshots/2.png?raw=true)
![SQL Query](https://github.com/iketiunn/electron-php-adminer/blob/main/screenshots/3.png?raw=true)

## Security Warning

This application runs PHP inside Electron, which presents several security concerns:
- PHP code execution happens outside of Electron's security sandbox
- The application has full system access through both Node.js and PHP
- Adminer's direct access to databases may expose sensitive information

**Not recommended for production use or with sensitive databases.**

## Credits

- [Adminer](https://www.adminer.org/) - Database management in a single PHP file
- [static-php-cli](https://github.com/crazywhalecc/static-php-cli) - Portable PHP interpreter 
- [Lukáš Brandejs](https://raw.githubusercontent.com/vrana/adminer/master/designs/ng9/adminer.css) - CSS design used in Adminer
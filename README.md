# CI4 HMVC Generator

CI4 HMVC Generator is a powerful toolkit designed to facilitate the implementation of the Hierarchical Model-View-Controller (HMVC) pattern in CodeIgniter 4 projects. This package provides a set of command-line tools to easily generate and manage HMVC modules, controllers, and models.

## Requirements

- PHP 7.4 or later
- CodeIgniter 4.5.3 or later

## Installation

You can install this package via Composer. Run the following command in your terminal:

bash
composer require your-vendor-name/ci4-hmvc-generator

Usage
This package provides the following commands:

Setup HMVC Structure
bash
php spark hmvc:setup
This command sets up the basic HMVC structure in your CodeIgniter 4 project.

Create a New HMVC Module
bash
php spark hmvc:module ModuleName
This command creates a new HMVC module with the specified name.

Generate a New Controller
bash
php spark hmvc:controller ModuleName ControllerName
This command generates a new controller within the specified HMVC module.

Generate a New Model
bash
php spark hmvc:model ModuleName ModelName
This command generates a new model within the specified HMVC module.

Structure
After setting up HMVC, your project structure will look like this:
app/
└── Modules/
    └── YourModule/
        ├── Config/
        │   └── Routes.php
        ├── Controllers/
        ├── Models/
        └── Views/

Contributing
Contributions are welcome! Please feel free to submit a Pull Request.
License
This project is licensed under the MIT License - see the LICENSE.md file for details.
Support
If you discover any security-related issues, please email sergiogarciamamani@gmail.com instead of using the issue tracker.

Credits
Sergio Garcia Mamani
Adalid Alanoca Ramirez

About
CI4 HMVC Generator is a package developed to enhance the modular capabilities of CodeIgniter 4. We're dedicated to making development with CodeIgniter 4 more efficient and enjoyable.

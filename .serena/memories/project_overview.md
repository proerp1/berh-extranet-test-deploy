# BeRH Extranet - Project Overview

## Purpose
BeRH Extranet is a benefits management system for managing employee benefits, customer users, and benefit assignments. The system handles benefit delivery, tracking, and reporting for corporate clients.

## Tech Stack
- **Framework**: CakePHP 2.x (MVC pattern)
- **Language**: PHP
- **Database**: MySQL
- **Dependencies**: 
  - robmorgan/phinx (migrations)
  - mpdf/mpdf (PDF generation)
  - phpoffice/phpspreadsheet (Excel reports)
  - sendgrid/sendgrid (email)
  - firebase/php-jwt (authentication)

## Project Structure
```
/app
  /Controller - Application controllers (CustomerUsersController, etc.)
  /Model - Data models (CustomerUser, Benefit, CustomerUserItinerary, etc.)
  /View - View templates (.ctp files)
  /Config - Configuration files
  /Lib - Library files
  /Private - Private files (Excel templates, etc.)
  /Plugin - CakePHP plugins
/migrations - Database migrations (Phinx)
/vendor - Composer dependencies
```

## Key Models
- **CustomerUser**: Employee/beneficiary records with CPF, matricula, status
- **Benefit**: Benefit types available (transport, meal, etc.)
- **CustomerUserItinerary**: Junction table linking CustomerUser to Benefit (benefits assigned to users)
- **Order/OrderItem**: Orders for benefit delivery
- **Status**: Generic status table used across entities

## Naming Conventions
- Controllers: PascalCase with "Controller" suffix (CustomerUsersController)
- Models: PascalCase singular (CustomerUser, Benefit)
- Database tables: snake_case plural (customer_users, benefits, customer_user_itineraries)
- Methods: camelCase or snake_case
- Views: snake_case.ctp files
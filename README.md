# Bank Transaction Service

## Description

This PHP Laravel based service handles bank transactions between two bank cards. It manages the transaction process, validities, and notifications to both the sender and receiver.

## Key Features

- Perform transactions between two bank cards.
- Ensure sufficient funds in the origin card before performing a transaction.
- Retrieve transaction fee details from the application configuration.
- Record each transaction detail to the database, including a separate record for transaction fees.
- Sends SMS notification to both the sender and receiver upon successful completion of the transaction.
## Prerequisites

Docker on your system is required. also composer for first run.

## Installation Guide

```bash 
./vendor/bin/sail up -d
```

```bash
./vendor/bin/sail composer install
```

```bash
./vendor/bin/sail artisan migrate --seed
```
## Testing


```bash
./vendor/bin/sail test
```

## Contact Information

- [Email](mailto:armohammady76@gmail.com)

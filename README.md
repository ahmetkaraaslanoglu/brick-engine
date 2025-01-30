# BrickEngine

**BrickEngine** is a simple, flexible, and extensible script engine written in PHP.  
It allows you to manage custom business rules, e-commerce discounts, automation scenarios, or any workflow you define without "changing code."

> **Status:** The project is still in development and may not yet be fully ready for production use.  
> **Support Us:** If you like the project, please star it or contribute to support its development.

---

# Why Choose BrickEngine?

## Features

- **Dynamic Scripting Language:** BrickEngine supports a wide range of operations through its flexible scripting language.
- **Easy Extensibility:** You can add your own functions (e.g., `apply_discount`, `dump`, `json_decode`) and variables to the context. All functions and variables must be explicitly defined and bound to BrickEngine.
- **API & External Integrations:** Whether for e-commerce, payment systems, or microservices, you can utilize scripts to make external API calls.
- **Clean and Readable Syntax:** Easily perform conditional operations using simple `if` structures, loops, and assignments.
- **Performance and Flexibility:** Centralize and manage your business rules to avoid complex branching and if-else chains.
- **Support for Various Data Types:** Operate on different data types, such as strings, numbers, booleans, and arrays.
- **Loop and Conditional Structures:** Includes support for control structures like `while`, `if-else`, and `for` loops.

---

## Installation

**Via Composer**

```bash
composer require isaeken/brick-engine
```

## Quick Start

The example below demonstrates a simple script that applies a discount if the cart total exceeds 100 units and then processes an e-commerce scenario by fetching a response from an external API.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use IsaEken\BrickEngine\BrickEngine;

$script = <<<BRICK
if (cart['total'] > 100) {
    apply_discount();
    return "You have a discount!";
}

cart = {
    total: cart['total'],
    isPaid: false,
    sendApiRequest: function (url) {
        response = fetch(url);
        response = json_decode(response);
        return response['message'];
    },
};

return {
    success: true,
    message: cart.sendApiRequest('https://api.example.com'),
};
BRICK;

$engine = new BrickEngine();

// Define functions to be used within the script
$engine->context->setFunction('apply_discount', function () use ($engine) {
    // resolve variable value
    $value = fromValue($engine->context->variables['total']);
    
    return $value - 10;
});

// Define variables to be used in the script
$engine->context->setVariable('total', 120);

$result = $engine->run($script)->value->data;
echo $result; // "You have a discount!" or API message
```
**How It Works**

1. The `BrickEngine` class interprets and executes the *script*.
2. Functions in the `context->functions` array can be directly accessed within the script.
3. The `context->variables` array defines variables that can be accessed in the script, such as `cart['total']`.

## Running in Docker

You can run the example script in a Docker container using the following commands:

```bash
docker build -t brick-engine .
docker run --rm -v "$(pwd)":/app brick-engine ./examples/example.bee
```

## Use Cases

- **E-Commerce Rules:** Create dynamic business rules for cart totals, shipping cost calculations, or payment steps.
- **Form Validation / Workflow:** Process user inputs and direct them to different scenarios.
- **Content Management:** Apply dynamic rule sets for news, blogs, or similar content platforms.
- **API & Microservice Integration:** Manage external API calls based on specific conditions (e.g., order confirmation, payment processing).
- **Game / Application Logic:** Manage simple rule sets for game servers or create rapid prototypes using mini-scripts within applications.

## Roadmap

Read the full [ROADMAP.md](ROADMAP.md) file for more details on the project's future development plans.

## License

This project is licensed under the MIT License. For more information, see the [LICENSE.md](LICENSE.md) file.

## Contact & More Information

- **Developer / Founder:** İsa Eken (hello@isaeken.com.tr)
- **GitHub:** [İsa Eken](https://github.com/isaeken)
- **LinkedIn:** [İsa Eken](https://www.linkedin.com/in/isaeken)
- **Website:** [https://www.isaeken.com.tr](https://www.isaeken.com.tr)


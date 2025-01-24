# BrickEngine

BrickEngine is a simple scripting language that is designed to scriptable web applications.

> **Note:** This project is still in development and not ready for production.
> If you like the project and want to support, you can support me by giving a star, you can contribute to the project, or you can donate.

## Examples

```php
$script = <<<BRICK
if (cart['total'] > 100) {
    apply_discount();
    return "You have a discount!";
}

response = fetch('https://api.example.com');
response = json_decode(response);
return response['message'];
BRICK;

$engine = new BrickEngine();
$engine->context->functions['apply_discount'] = function () {
    $this->context->cart['total'] -= 10;
};
$engine->context->variables['cart'] = ['total' => 120];
$message = $engine->run($script)->value->data;
echo $message;
```

# BrickEngine

BrickEngine is a simple scripting language that is designed to scriptable web applications.

## Examples

### Hello World

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

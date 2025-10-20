# Sylius Integration Plugin

***

Official [Synerise](https://www.synerise.com) plugin allowing configurable integration of [Sylius](https://sylius.com) applications with the platform. Main features include tracking customer actions (e.g. cart and transaction events) as well as data synchronisation of customers, orders and product catalog. The Synerise platform provides a variety of tools for building well-targeted and AI powered omnichannel marketing campaigns for any store. 

## Requirements

Plugin usage requires access to the Synerise platform. Visit the [company website](https://www.synerise.com) to learn about the platform and its numerous features. There you can find useful resources, such as case studies and use cases, or request a demo presentation.

### Technical requirements

Before integration, please make sure your application meets with the following requirements:

* PHP 8.2+
* Sylius 2.x
* RabbitMQ (optional, but recommended as a message queues handler)

## Installation

Plugin is available as a composer package. Its registration is handled with Symfony Flex recipe. To benefit from autoconfiguration, please make sure that flex is available in your setup.

### 1. Allow Symfony contrib recipes

```bash
composer config extra.symfony.allow-contrib true
```

###  2. Include Synerise recipes repository

```bash
composer config --json --merge extra.symfony.endpoint '["https://api.github.com/repos/synerise/symfony-recipes/contents/index.json?ref=flex/main"]'
```

Alternatively, edit `composer.json` file of your application to include an endpoint of Synerise recipes repository:

```json
    "extra": {
        "symfony": {
            "endpoint": [
                "https://api.github.com/repos/synerise/symfony-recipes/contents/index.json?ref=flex/main",
                "..."
            ]
        }
        "..."
    }
```

### 3. Install the plugin with composer

```bash
composer require synerise/sylius-integration-plugin
```

### 4. Run doctrine migrations

```bash
bin/console doctrine:migrations:migrate
```
Remember to use `-e prod` option for production environment.

### 5. Build assets

```bash
yarn encore dev # for development
yarn encore production # for production
```

### 6. Clear the application cache

```bash
bin/console cache:clear
```

## Installation without flex

Although autoconfiguration with flex is the recommended option, the plugin can also be registered manually. 

### 1. Register bundle

Edit your bundles config file `config/bundles.php` and add plugin to the array:
```
<?php

return [
    ...
    Synerise\SyliusIntegrationPlugin\SyneriseSyliusIntegrationPlugin::class => ['all' => true],
];
```

### 2. Copy config files

Head over to [recipes repository](https://github.com/Synerise/symfony-recipes/tree/flex/main/synerise/sylius-integration-plugin/). Select the appropriate version. Copy contents of config directory you application config.

### 3. Add admin entrypoint 

Edit `assets/admin/entrypoint.js` script by adding the following lines:
```
    import '../../vendor/synerise/sylius-integration-plugin/assets/admin/entrypoint
```

## Documentation
To learn how to properly configure the plugin, please head to our [documentation](https://hub.synerise.com/docs/settings/tool/sylius-integration/) page.

## Support
In case of a bug, feature request or any trouble with the integration, visit our [support](http://synerise.com/support/) page and fill in the proper form. 

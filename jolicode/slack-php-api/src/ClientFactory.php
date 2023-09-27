<?php

declare(strict_types=1);

/*
 * This file is part of JoliCode's Slack PHP API project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliCode\Slack;

use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JoliCode\Slack\HttpPlugin\AddSlackPathAndHostPlugin;
use JoliCode\Slack\HttpPlugin\SlackErrorPlugin;
use Psr\Http\Client\ClientInterface;

class ClientFactory
{
    public static function create(string $token, ClientInterface $httpClient = null): Client
    {
        // Find a default HTTP client if none provided
        if (null === $httpClient) {
            $httpClient = Psr18ClientDiscovery::find();
        }

        // Decorates the HTTP client with some plugins
        $uri = Psr17FactoryDiscovery::findUriFactory()->createUri('https://slack.com/api');
        $pluginClient = new PluginClient($httpClient, [
            new ErrorPlugin(),
            new SlackErrorPlugin(),
            new AddSlackPathAndHostPlugin($uri),
            new HeaderAppendPlugin([
                'Authorization' => 'Bearer ' . $token,
            ]),
        ]);

        // Instantiate our client extending the one generated by Jane
        return Client::create($pluginClient);
    }
}

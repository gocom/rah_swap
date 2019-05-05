<?php

/*
 * rah_swap - Database swapper for Textpattern CMS
 * https://github.com/gocom/rah_swap
 *
 * Copyright (C) 2019 Jukka Svahn
 *
 * This file is part of rah_swap.
 *
 * rah_swap is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * rah_swap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with rah_swap. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Plugin class.
 */
final class Rah_Swap
{
    /**
     * Stores the original configuration.
     *
     * @var array
     */
    private $defaultConfig;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->defaultConfig = $this->getConfig();
    }

    /**
     * Gets the configuration.
     *
     * @return array
     */
    private function getConfig(): array
    {
        global $txpcfg;
        return $txpcfg;
    }

    /**
     * Sets the configuration.
     *
     * @return $this
     */
    private function setConfig(array $config): self
    {
        global $txpcfg;
        $txpcfg = array_merge($this->defaultConfig, $config);
        return $this;
    }

    /**
     * Whether the call is allowed within the  template context.
     *
     * @return bool
     */
    private function isAllowed(): bool
    {
        return parse('<txp:php> echo "true"; </txp:php>') === 'true';
    }

    /**
     * Gets an array of links.
     *
     * @return array
     */
    private function getLinks(): array
    {
        global $rah_swap;
        return (array) $rah_swap;
    }

    /**
     * Gets a link by name.
     *
     * @param  string $name The name
     * @return array
     */
    private function getLinkByName(string $name): array
    {
        $links = $this->getLinks();

        if (!isset($links[$name])) {
            throw new \InvalidArgumentException('Invalid link name given');
        }

        return $links[$name];
    }

    /**
     * Change used database.
     *
     * @return $this
     */
    private function setUsedDatabase(string $name): self
    {
        global $DB;
        mysqli_select_db($DB->link, $name);
        return $this;
    }

    /**
     * Reconnect using the configuration.
     *
     * @return $this
     */
    private function connect(): self
    {
        global $DB;
        mysqli_close($DB->link);
        $this->setConfig($this->getConfig());
        $DB = new DB;
        return $this;
    }

    /**
     * Resets connection back to as it was during initialization.
     *
     * @return $this
     */
    private function reset(): self
    {
        return $this->setConfig([])->connect();
    }

    /**
     * Renders swap tag.
     *
     * @param  array       $atts  Attributes
     * @param  string|null $thing Contained statement
     * @return string
     */
    public function renderSwap(array $atts, $thing = null): string
    {
        global $rah_swap, $txpcfg, $DB;

        if ($this->isAllowed() === false) {
            return '';
        }

        if (isset($atts['link'])) {
            try {
                $atts = (array) $this->getLinkByName($atts['link']);
            } catch (\InvalidArgumentException $e) {
                trigger_error(gTxt('invalid_attribute_value', ['{name}' => $atts['link']]));
                return '';
            }
        }

        $opt = lAtts([
            'link' => '',
            'reset' => false,
            'db' => null,
            'user' => '',
            'pass' => '',
            'host' => 'localhost',
            'dbcharset' => 'utf8',
            'client_flags' => 0,
        ], $atts);

        extract($opt);

        if (count($atts) === 1 && $db !== null) {
            $this->setUsedDatabase($db);
        } elseif (!$reset) {
            $this->setConfig($opt)->connect();
        }

        if ($thing !== null) {
            $reset = true;
            $r = parse($thing);
        } else {
            $r = '';
        }

        if ($reset) {
            $this->reset();
        }

        return $r;
    }
}

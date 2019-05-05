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

Txp::get('\Textpattern\Tag\Registry')->register([new Rah_Swap(), 'renderSwap'], 'rah_swap');

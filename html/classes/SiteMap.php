<?php

const SITEMAP_URLSET = 'urlset';
const SITEMAP_URLSET_XMLNS = 'http://www.sitemaps.org/schemas/sitemap/0.9';

####

const SITEMAP_NODE_URL = 'url';
const SITEMAP_NODE_LOC = 'loc';
const SITEMAP_NODE_LASTMOD = 'lastmod';
const SITEMAP_NODE_CHANGEFREQ = 'changefreq';
const SITEMAP_NODE_PRIORITY = 'priority';

####

const SITEMAP_CHANGEFREQ_ALWAYS = 'always';
const SITEMAP_CHANGEFREQ_HOURLY = 'hourly';
const SITEMAP_CHANGEFREQ_DAILY = 'daily';
const SITEMAP_CHANGEFREQ_WEEKLY = 'weekly';
const SITEMAP_CHANGEFREQ_MONTHLY = 'monthly';
const SITEMAP_CHANGEFREQ_YEARLY = 'yearly';
const SITEMAP_CHANGEFREQ_NEVER = 'never';

####

const SITEMAP_ADDRESS_PAGE = '/page/';
const SITEMAP_ADDRESS_SHOP_SECTION = '/shop/section/';
const SITEMAP_ADDRESS_SHOP_ITEM = '/shop/item/';

####

const SITEMAP_PRIORITY_PAGE = '0.5';
const SITEMAP_PRIORITY_SECTION = '0.2';
const SITEMAP_PRIORITY_ITEM = '1';

####

class SiteMap
{
	public function __construct()
	{
		$this->dom = new DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;

		$this->root = $this->dom->createElement(SITEMAP_URLSET);
		$this->root->setAttribute(SITEMAP_URLSET, SITEMAP_URLSET_XMLNS);
		$this->dom->appendChild($this->root);

		$this->arrayPages = array();
	}

	public function run()
	{
		$this->loadFromDB();

		foreach($this->arrayPages as $iter)
		{
			switch($iter['component'])
			{
				case 'page':
				{
					$address = 'http://'.$_SERVER['SERVER_NAME'].SITEMAP_ADDRESS_PAGE.$iter['id'];
					$this->addAddress($address, '', SITEMAP_CHANGEFREQ_MONTHLY, SITEMAP_PRIORITY_PAGE);
				} break;

				case 'shop':
				{
					switch($iter['p1'])
					{
						case 'item':
						{
							$address = 'http://'.$_SERVER['SERVER_NAME'].SITEMAP_ADDRESS_SHOP_ITEM.$iter['id'];
							$this->addAddress($address, '', SITEMAP_CHANGEFREQ_WEEKLY, SITEMAP_PRIORITY_ITEM);
						} break;
					}
				} break;
			}
		}

		$this->addAddress('http://'.$_SERVER['SERVER_NAME'].'/shop/filter/&section=1', '', SITEMAP_CHANGEFREQ_DAILY, SITEMAP_PRIORITY_SECTION);
		$this->addAddress('http://'.$_SERVER['SERVER_NAME'].'/shop/filter/&section=2', '', SITEMAP_CHANGEFREQ_DAILY, SITEMAP_PRIORITY_SECTION);
		$this->addAddress('http://'.$_SERVER['SERVER_NAME'].'/shop/filter/&section=3', '', SITEMAP_CHANGEFREQ_DAILY, SITEMAP_PRIORITY_SECTION);
	}

	public function save($_path)
	{
		if(strlen($_path) == 0)
			return false;

		$this->dom->save($_path);
		return true;
	}

	private function loadFromDB()
	{
		$SQL_MENU = mysql_query('SELECT `name`, `component`, `p1`, `id_com` FROM `menu` WHERE `pub` = 1');
		while($iter = mysql_fetch_array($SQL_MENU))
		{
			$this->arrayPages[] = array(
				'id'  => $iter['id_com'],
				'name' => $iter['name'],
				'component' => $iter['component'],
				'p1' => $iter['p1']
			);
		}

		$SQL_ITEMS = mysql_query('SELECT `id`, `title` FROM `com_shop_item` WHERE `pub` = 1');
		while($iter = mysql_fetch_array($SQL_ITEMS))
		{
			$this->arrayPages[] = array(
				'id' => $iter['id'],
				'name' => $iter['title'],
				'component' => 'shop',
				'p1' => 'item',
			);
		}
	}

	private function addAddress($_address, $_lastMod, $_changeFreq, $_priority)
	{
		if(strlen($_address) == 0)
			return false;

		$url = $this->dom->createElement(SITEMAP_NODE_URL);

		$url->appendChild(
			$this->dom->createElement(SITEMAP_NODE_LOC, htmlspecialchars($_address))
		);

		if(strlen($_lastMod) > 0)
		{
			$url->appendChild(
				$this->dom->createElement(SITEMAP_NODE_LASTMOD, $_lastMod)
			);
		}

		if(strlen($_changeFreq) > 0)
		{
			$url->appendChild(
				$this->dom->createElement(SITEMAP_NODE_CHANGEFREQ, $_changeFreq)
			);
		}

		if(strlen($_priority) > 0 && $_priority >= 0 && $_priority <= 1)
		{
			$url->appendChild(
				$this->dom->createElement(SITEMAP_NODE_PRIORITY, $_priority)
			);
		}

		$this->root->appendChild($url);
		return true;
	}

	private $dom;
	private $root;
	private $arrayPages;
};

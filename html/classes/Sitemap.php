<?php
include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

####

const SITEMAP_CHANGEFREQ_ALWAYS = 'always';
const SITEMAP_CHANGEFREQ_HOURLY = 'hourly';
const SITEMAP_CHANGEFREQ_DAILY = 'daily';
const SITEMAP_CHANGEFREQ_WEEKLY = 'weekly';
const SITEMAP_CHANGEFREQ_MONTHLY = 'monthly';
const SITEMAP_CHANGEFREQ_YEARLY = 'yearly';
const SITEMAP_CHANGEFREQ_NEVER = 'never';

####

const SITEMAP_PRIORITY_PAGE = '0.5';
const SITEMAP_PRIORITY_SECTION = '0.2';
const SITEMAP_PRIORITY_ITEM = '1';

####

class Sitemap
{
	public function __construct()
	{
		$this->dom = new DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;

		$this->root = $this->dom->createElement('urlset');
		$this->root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$this->dom->appendChild($this->root);

		$this->loadFromDB();
	}

	public function run()
	{
		foreach($this->arrayPages as $iter)
		{
			switch($iter['component'])
			{
				case 'page':
				{
					$address = 'http://'.$_SERVER['SERVER_NAME'].'/page/'.$iter['id'];
					$this->addAddress($address, SITEMAP_CHANGEFREQ_MONTHLY, SITEMAP_PRIORITY_PAGE);
				} break;

				case 'shop':
				{
					switch($iter['p1'])
					{
						case 'section':
						{
							$address = 'http://'.$_SERVER['SERVER_NAME'].'/shop/section/'.$iter['id'];
							$this->addAddress($address, SITEMAP_CHANGEFREQ_WEEKLY, SITEMAP_PRIORITY_SECTION);
						} break;

						case 'item':
						{
							$address = 'http://'.$_SERVER['SERVER_NAME'].'/shop/item/'.$iter['id'];
							$this->addAddress($address, SITEMAP_CHANGEFREQ_WEEKLY, SITEMAP_PRIORITY_ITEM);
						} break;
					}
				} break;
			}
		}
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
		global $db;

		$SQL_PREPARE = $db->query('SELECT name, component, p1, id_com FROM menu WHERE pub = 1 and component IN ("page", "shop")');

		foreach($SQL_PREPARE->fetchAll() as $iter)
		{
			$this->arrayPages[] = array(
				'id'  => $iter['id_com'],
				'name' => $iter['name'],
				'component' => $iter['component'],
				'p1' => $iter['p1']
			);
		}

		$SQL_PREPARE = $db->query('SELECT id, title FROM com_shop_item WHERE pub = 1');

		foreach($SQL_PREPARE->fetchAll() as $iter)
		{
			$this->arrayPages[] = array(
				'id'  => $iter['id'],
				'name' => $iter['title'],
				'component' => 'shop',
				'p1' => 'item'
			);
		}
	}

	private function addAddress($_address, $_changeFreq, $_priority)
	{
		if(strlen($_address) == 0)
			return false;

		$url = $this->dom->createElement('url');

		$url->appendChild(
			$this->dom->createElement('loc', htmlspecialchars($_address))
		);

		if(strlen($_changeFreq) > 0)
		{
			$url->appendChild(
				$this->dom->createElement('changefreq', $_changeFreq)
			);
		}

		if(strlen($_priority) > 0 && $_priority >= 0 && $_priority <= 1)
		{
			$url->appendChild(
				$this->dom->createElement('priority', $_priority)
			);
		}

		$this->root->appendChild($url);
		return true;
	}

	private $dom;
	private $root;
	private $arrayPages = array();
};

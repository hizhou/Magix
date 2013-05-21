<?php
namespace Magix\Utility\ConfigLoader;

interface IConfigLoader {
	public function getConfig($name, $option = null);
}

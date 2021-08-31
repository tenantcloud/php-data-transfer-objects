<?php

$finder = PhpCsFixer\Finder::create()
	->in('src')
	->in('tests')
	->name('*.php')
	->notName('_*.php')
	->ignoreVCS(true);

return (new PhpCsFixer\Config())
	->setFinder($finder)
	->setRiskyAllowed(true)
	->setIndent("\t")
	->setRules([
		'@TenantCloud' => true,
	]);

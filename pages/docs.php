<?php

$mdFiles = [];
foreach (glob(rex_addon::get('yform')->getPath('docs').'/*.md') as $file) {
    $mdFiles[substr(basename($file), 0, -3)] = $file;
}

$currenMDFile = rex_request('mdfile', 'string', 'intro');
if (!array_key_exists($currenMDFile, $mdFiles)) {
    $currenMDFile = '01_intro';
}

$page = \rex_be_controller::getPageObject('yform/docs');

foreach ($mdFiles as $key => $mdFile) {
    $keyWithoudPrio = substr($key, 3);
    $currenMDFileWithoudPrio = substr($currenMDFile, 3);
    $page->addSubpage((new rex_be_page($key, rex_i18n::msg('yform_docs_'.$keyWithoudPrio)))
        ->setSubPath($mdFile)
        ->setHref('index.php?page=yform/docs&mdfile='.$key)
        ->setIsActive($key == $currenMDFile)
    );
}

echo rex_view::title($this->i18n('yform'));

[$Toc, $Content] = rex_markdown::factory()->parseWithToc(rex_file::require($mdFiles[$currenMDFile]), 2, 3, [
    rex_markdown::SOFT_LINE_BREAKS => false,
    rex_markdown::HIGHLIGHT_PHP => true,
]);

$fragment = new rex_fragment();
$fragment->setVar('content', $Content, false);
$fragment->setVar('toc', $Toc, false);
$content = $fragment->parse('core/page/docs.php');

$fragment = new rex_fragment();
// $fragment->setVar('title', rex_i18n::msg('package_help') . ' ', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


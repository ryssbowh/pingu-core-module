<?php
namespace Modules\Core\Components;

use Modules\Core\Entities\TextSnippet as SnippetModel;
use Modules\Core\Events\TextSnippetTextRetrieved;

class TextSnippet
{
	public function create(string $name, string $text)
	{
		if($snippet = $this->getSnippet($name)) return false;
		$snippet = new SnippetModel();
		$snippet->name = $name;
		$snippet->text = $text;
		$snippet->save();
		return $snippet;
	}

	public function getSnippet($name)
	{
		return SnippetModel::where('name', $name)->get()->first();
	}

	public function getText(string $name, array $replacements = [])
	{
		if($snippet = $this->getSnippet($name)){
			event(new TextSnippetTextRetrieved($snippet, $replacements));
			return $this->replacements($snippet->text, $replacements);
		}
		return null;
	}

	public function getTextOrCreate(string $name, array $replacements = [], string $text = 'No text')
	{
		if(!$snippet = $this->getSnippet($name)){
			$snippet = $this->create($name, $text);
		}
		event(new TextSnippetTextRetrieved($snippet, $replacements));
		return $this->replacements($snippet->text, $replacements);
	}

	public function replacements(string $text, array $replacements)
	{
		foreach($replacements as $key => $replacement){
			$text = preg_replace('/\$\{'.$key.'\}/', $replacement, $text);
		}
		return $text;
	}
}
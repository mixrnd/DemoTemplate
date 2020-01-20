**Задание:** 
Напишите composer библиотеку реализующую простой шаблонизатор.

Шаблонизатор должен поддерживать переменные и блок if.
Гарантируется что переданный шаблон синтаксически корректен.
Возможны вложенные if.
Возможны if с else и без.
Наличие тестов будет плюсом.

Пример шаблона:
~~~~
<div>{my_var}</div>
{if bool_var}
    True
{else}
    False
{/if}
~~~~
Пример вывода с перменными my_var="test", bool_var=true:
~~~~
<div>test</div>
True
~~~~

Пример вызова:
~~~~
$parser = new \mixrnd\DemonTemplate\DemoTemplate();
echo  $parser->parse(
    '<div>{my_var}</div>{you_var} <p>{p_var}</p>', 
    ['my_var' => 'test', 'you_var' => 'test2', 'p_var' => 'test3']
    );
~~~~

Шаблонизатор реализован как интерпретатор, методом рекурсивного спуска.

Через composer можно установить, добавив в composer.json
~~~~
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/mixrnd/DemoTemplate.git"
    }
  ],
  "require": {
    "mixrnd/DemoTemplate": "dev-master"
  }
}
~~~~

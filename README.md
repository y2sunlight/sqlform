# sqlform

Sqlform はブラウザ上のフォームからSQLスクリプトを実行できるツールで、スクリプトの編集、保存、呼び出し、削除ができます。テスト時、保守などで利用可能です。ログイン機能を実装していないので、運用時はhttpsにてWebサーバの認証機能を利用するか、認証機能付きのアプリケーションに組み込んでご利用下さい。

## クイックスタート

[リリース版](https://github.com/y2sunlight/sqlform/releases)をダウンロードし、適当な場所に解凍して下さい。

ダウンロードした `sqlform.php` ファイルの存在するディレクトリでPHPのビルトインサーバーを実行します。

~~~
php -S localhost:8888
~~~

そして、以下のURLにアクセスしてください。

* http://localhost:8888/sqlform.php

![sample01](http://www.y2sunlight.com/ground/lib/exe/fetch.php?media=sqlform:usage:ja:sample01b.png)

起動後には、以下のsqlスクリプトが表示されています。

~~~
SELECT 'Hello, World!' as phrase;
~~~

画面右上のプレイボタン（3つ並んだ三番目のボタン）を押すと、実行結果が表示されます。

![sample02](http://www.y2sunlight.com/ground/lib/exe/fetch.php?media=sqlform:usage:ja:sample02b.png)

> 解凍直後は、データーベースとして *SQLite* が選択されています。SQLiteが使用できない環境、または他のデータベースを構成したい場合は、以下の「ドキュメント」を参照して下さい。

## ドキュメント
Sqlformの使い方、構成、SQLファイルの仕様については以下のサイトをご覧下さい。

* [y2sunlight.com](http://y2sunlight.com/ground/doku.php?id=sqlform:usage:ja)

## License
The sqlfile is licensed under the MIT license. See [License File](LICENSE) for more information.

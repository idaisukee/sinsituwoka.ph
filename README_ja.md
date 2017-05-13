# 手順

http://qiita.com/shin1ogawa/items/49a076f62e5f17f18fe5 も参照．

Google の site で json をもらって `constants/client_secret.json` に置く．

`show_uri.php` を実行して認可する．表示された code を `constants/authorization_code` に置く．

`get_access_token.php` を実行して，結果を `constants/access_token.json` に置く．

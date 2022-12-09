<?php

//セッションIDがなかったら、ログインしていないので、ログイン画面へ行く。
function check_session_id()
{
  if (!isset($_SESSION["session_id"]) || $_SESSION["session_id"] != session_id()) {
    $_SESSION["message"] = "ログインが必要です。";
    header("Location:./../../index.html");
    exit();
  } else {
    session_regenerate_id(true);
    $_SESSION["session_id"] = session_id();
  }
}

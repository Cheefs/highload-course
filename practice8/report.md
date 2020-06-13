####1. Установить Zabbix Server.
Устаналивал заббикс по офф доке, и проблем с php небыло, да и в методичке плохо все описанноо
https://www.zabbix.com/download?zabbix=5.0&os_distribution=debian&os_version=10_buster&db=mysql&ws=nginx

Была одна ошибка - явместо хоста вписывал названия Базы, не читая что ему надо, он ругался, прочитав ошибку поправил. и все завелось написало следующее

`Install
Congratulations! You have successfully installed Zabbix frontend.
Configuration file "/usr/share/zabbix/conf/zabbix.conf.php" created.`

* На вход в интерфейс нужно вводить

      логин: `Admin` ( c большой буквы, в методичке не верно, у меня не пускало с логином admin ) 
      пароль: `zabbix`

* статус сервиса:

      root@user:/etc/nginx/sites-available# systemctl status zabbix-server.service 
      ● zabbix-server.service - Zabbix Server
        Loaded: loaded (/lib/systemd/system/zabbix-server.service; disabled; vendor preset: enabled)
        Active: active (running) since Sun 2020-06-07 09:04:23 +07; 2min 45s ago
        Process: 12828 ExecStop=/bin/kill -SIGTERM $MAINPID (code=exited, status=0/SUCCESS)
        Process: 12830 ExecStart=/usr/sbin/zabbix_server -c $CONFFILE (code=exited, status=0/SUCCESS)
      Main PID: 12856 (zabbix_server)
          Tasks: 1 (limit: 4915)
        CGroup: /system.slice/zabbix-server.service
                └─12856 /usr/sbin/zabbix_server -c /etc/zabbix/zabbix_server.conf
                июн 07 09:04:23 user systemd[1]: zabbix-server.service: Main process exited, code=exited, status=1/FAILURE
      июн 07 09:04:23 user systemd[1]: zabbix-server.service: Failed with result 'exit-code'.
      июн 07 09:04:23 user systemd[1]: Stopped Zabbix Server.
      июн 07 09:04:23 user systemd[1]: Starting Zabbix Server...
      июн 07 09:04:23 user systemd[1]: zabbix-server.service: Can't open PID file /run/zabbix/zabbix_server.pid (yet?) after start: No such file or directory
      июн 07 09:04:23 user systemd[1]: Started Zabbix Server.

* В конфиге забикса `/etc/zabbix/zabbix_server.conf`, расскоментировал параметры:

      StartTrappers=5
      ListenPort=10051

* В конфиге забикса для php-fpm `/etc/zabbix/php-fpm.conf` расскоментировал часовой пояс, и добавил свой
php_value[date.timezone] = Asia/Novosibirsk

* Для веб интерфейса в конфиг `/etc/zabbix/web/zabbix.conf.php` добавил: 
        DBHost=localhost
        DBName=zabbix
        DBUser=zabbix
        DBPassword=password


* Проблемы при установке

      Били проблемы, заббикс сервер не мог подключится к mysql
      и в веб интерфейсе его статус всегда был no ( выключен )

      Помогло прописать в файлах 
      /etc/zabbix/zabbix_server.conf
      /etc/zabbix/web/zabbix.conf.php
      Хост и порт базы с localhost и порт был 0 ( использует дефольные настройки, для пострегсс свой для mysql свой )
      заменил их на 127.0.0.1 3306 и все запустилось

      выхлоп статуса из веб морды:

      Zabbix server is running	Yes	localhost:10051
      Number of hosts (enabled/disabled/templates)	146	1 / 0 / 145
      Number of items (enabled/disabled/not supported)	88	84 / 0 / 4
      Number of triggers (enabled/disabled [problem/ok])	48	48 / 0 [0 / 48]
      Number of users (online)	2	1
Required server performance, new values per second	1.27	

### 2. Добавить шаблон мониторинга HTTP-соединений.

  Для мониторнига соеденений, я подключил свой шаблон, с мониторгингом соедениний к сайту
  Там все в веб интерфейсе настраивается, проблемы лиш в том чтоб привыкнуть где и что

### 3. Настроить мониторинг созданных в рамках курса виртуальных машин.

Данный пункт частично был уже реализован в пред идущем, потому что шаблон создавался уже там.
Но тут нужно было создать новый хост в интерфейса, и сделать ему настроку, и добавить веб настройки для вывода мониторинга 

* Далее на нужных виртуалках я устанавливал `zabbix-agent`
* В его конфиг `/etc/zabbix/zabbix_agentd.conf` вписывал ip сервера где расположен сервер мониторнига 
`Server=10.0.2.7`
* Далее настоил nginx, на получение статистики

      location = /basic_status {
          stub_status on;
          allow 127.0.0.1;
          deny all;
      }

Он по сути деграет этот новый location, и парсит его, на машине где это сделанно, можно посмотреть этот вывод прям в браузере перейдя на урл `http://localhost/basic_status`
И поллучить такой вывод:

    Active connections: 1 
    server accepts handled requests
    3 3 2 
    Reading: 0 Writing: 1 Waiting: 0 

После настройки агента на первом хосте, вывод в мониторинге забикса изменился:

    System information

    Parameter	Value	Details
    Zabbix server is running	Yes	localhost:10051
    Number of hosts (enabled/disabled/templates)	148	2 / 0 / 146
    Number of items (enabled/disabled/not supported)	171	159 / 0 / 12
    Number of triggers (enabled/disabled [problem/ok])	96	96 / 0 [0 / 96]
    Number of users (online)	2	1
    Required server performance, new values per second	2.29


Но также были ошибки, с настройками и доступом к статистике nginx, и когда соеденение было не возможно агент присылал Warning что ngix.service down, или not runing


### 4.* Добавить шаблон мониторинга NGINX.
    У забикса есть стандартные наборы шаблонов на мониторинг, nginx. Подключил их и активировал
    Также проделал работу с получением статистики как делал это на серверах с забикс агентами
setInterval(function(){
        //отправляем запрос на сервер
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php', true);
        xhr.send();

        //функция для обработки данных, полученных от сервера
        xhr.onreadystatechange = function() {
            if (this.readyState != 4) return; //Если ответ от сервера ещё не пришёл
            if (this.status != 200) return;

            //Когда от сервера получен новый HTML-код, находим в старом коде
            //элемент с ID=’App_interface’ и заменяем его на полученный код
            var i_face = document.getElementById('App_interface');
            if(i_face!=null) i_face.innerHTML = this.responseText;
        };
        delete(xhr);
},3000); //выполняем обновление каждые 3 секунды

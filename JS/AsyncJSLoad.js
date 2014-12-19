var modules = [
    [0,  'iteml', function(){ alert('item1 is loaded'); }],
    [1,  'item2', function(){ alert('item2 is loaded'); }],
    [1,  'item3', function(){ alert('item3 is loaded'); }]
];

/* перебор и загрузка модулей */
function load_by_parent (i) {
    i = i || 0;
    var len = modules.length, module;
    /* перебираем дерево модулей */
    while (len--) {
        module = modules[len];
        /* и загружаем требуемые элементы */
        if (!module[0]) {
            loader(len);
        }
    }
}

/* объявляем функцию-загрузчик */
function loader (i) {
    var module = modules[i];
    /* создаем новый элемент script */
    var script = document.createElement('script');
    script.type = 'text/javascript';
    /* задаем имя файла */
    script.src = module[1] + '.js';
    /* задаем текст внутри тега для запуска по загрузке */
    script.text = module[2];
    /* запоминаем текущий индекс модуля */
    script.title = i + 1;
    /* выставляем обработчик загрузки для IE */
    script.onreadystatechange = function() {
        if (this.readyState === 'loaded') {
            /* перебираем модули и ищем те, которые нужно загрузить */
            load_by_parent(this.title);
        }
    };

    /* выставляем обработчик загрузки для остальных */
    script.onload = function (e) {
        /* исполняем текст внутри тега (нужно только для Opera)*/
        if (/opera/i.test(navigator.userAgent)) {
            eval(e.target.innerHTML);
        }
        /* перебираем модули и ищем те, которые нужно загрузить */
        load_by_parent(this.title);
    };
    /* прикрепляем тег к документу */
    document.getElementsByTagName('head')[0].appendChild(script);
}
/* загружаем корневые элементы */

load_by_parent();
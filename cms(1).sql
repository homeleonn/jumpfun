-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Дек 18 2017 г., 16:54
-- Версия сервера: 5.7.16
-- Версия PHP: 5.6.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `options`
--

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `option_value` longtext NOT NULL,
  `autoload` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `options`
--

INSERT INTO `options` (`id`, `name`, `option_value`, `autoload`) VALUES
(1, 'theme', 'default', 'yes'),
(2, 'front_page', '1', 'yes'),
(3, 'front_page1', '2', 'no');

-- --------------------------------------------------------

--
-- Структура таблицы `postmeta`
--

CREATE TABLE `postmeta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `postmeta`
--

INSERT INTO `postmeta` (`id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1, 7, 'description', 'description test'),
(2, 7, 'description1', 'description test1'),
(4, 7, 'price', '50');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `tags` varchar(255) NOT NULL,
  `post_type` varchar(50) NOT NULL DEFAULT 'page',
  `parent` bigint(20) UNSIGNED NOT NULL,
  `autor` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'publish',
  `comment_status` enum('open','closed') NOT NULL DEFAULT 'closed',
  `comment_count` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `url`, `title`, `content`, `tags`, `post_type`, `parent`, `autor`, `status`, `comment_status`, `comment_count`, `visits`, `created`, `modified`) VALUES
(1, 'test1', 'Прокат лимузинов в городе Одесса', '<!--MENU1(-)-->\r\n	<section class="menu1 container">\r\n		<div class="col-small-4">\r\n			<div class="menu1-title-img"><img alt="Заказать лимузин VIP автомобиль на свадьбу в Одессе на " src="http://localhost/jump/content/themes/default/img/menu1/limo1.jpg"><a href="http://localhost/jump/limousines/"><span class="text-right-array">Смотреть</span></a></div>\r\n			<div class="menu1-content">\r\n				<div class="tit">Лимузины</div>\r\n				<!--<div class="cont">\r\n					Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n				</div>-->\r\n			</div>\r\n		</div>\r\n		<div class="col-small-4">\r\n			<div class="menu1-title-img"><img alt="Арендовать авто седан в Одессе у компании Дива" src="http://localhost/jump/content/themes/default/img/menu1/sedan1.jpg"><a href="http://localhost/jump/sedans/"><span class="text-right-array">Смотреть</span></a></div>\r\n			<div class="menu1-content">\r\n				<div class="tit">Седаны</div>\r\n			</div>\r\n		</div>\r\n		<div class="col-small-4">\r\n			<div class="menu1-title-img"><img alt="Взять напрокат микроавтобус в Одессе" src="http://localhost/jump/content/themes/default/img/menu1/micro1.jpg"><a href="http://localhost/jump/mikroautobuss/"><span class="text-right-array">Смотреть</span></a></div>\r\n			<div class="menu1-content">\r\n				<div class="tit">Микроавтобусы</div>\r\n			</div>\r\n		</div>\r\n	</section>\r\n	\r\n	<!--ABOUT US(-)-->\r\n	<section id="about" class="container">\r\n		<h2 class="center indent title">Прокат VIP автомобилей в Одессе. О нас</h2>\r\n		<div class="col-12 ">\r\n			<div class="center"><img src="http://localhost/jump/content/themes/default/img/logo.png" alt="Логотим транспортной компании Дива. Аренда лимузина Одессе"></div>\r\n			<div class="subtitle myred">«К нам ведут все дороги..От нас открываются все пути!»</div>\r\n			<div class="subtitle">Вас интересует хорошо проведенное время, итогом которого станут яркие и приятные воспоминания?</div>\r\n			<div class="col-6">\r\n				Вы заглянули к нам не случайно, ведь именно мы, предлагаем вам наш <strong>комфортабельный транспорт от 6 до 70 мест</strong>, <strong>прокат <a href="http://localhost/jump/limousines/">лимузинов</a>, <a href="http://localhost/jump/sedans/">седанов</a>, <a href="http://localhost/jump/mikroautobuss/">микроавтобусов</a>, VIP автомобилей</strong> со всеми удобствами для важных и незабываемых моментов в вашей жизни, <strong>свадьбу, бизнес поездку</strong>, познавательных и увлекательных <strong>экскурсий</strong>, безопасных перевозок и дальних поездок.\r\n			</div>\r\n			<div class="col-6 clearfix">\r\n				Транспортная компания «Дива» вот уже много лет предоставляет качественные услуги по <strong>аренде авто в Одессе</strong>. Надежный автопарк, включающий самые разнообразные автомобили от <strong><a href="http://bus-diva.com.ua" target="_blank">крупных автобусов</a> до легковых авто vip-класса</strong>, а также квалифицированные водители с международным опытом работы позволяют нам предложить своим клиентам максимально <strong>безопасный и комфортный прокат авто в Одессе</strong>. Обратившись к нам, мы поможем Вам с выбором и предложим оптимальные варианты <strong>аренды и проката авто на любое количество мест</strong>.\r\n			</div>\r\n			<div class="col-12 about-us">\r\n				<h2>Разнообразие выбора</h2>\r\n					<strong>Заказать транспортное средство</strong> у нас, можно как любого класса, так и пассажировместимости и какой бы транспорт Вы не выбрали \r\n		– от компактного <strong>микроавтобуса</strong> до шикарного <strong>лимузина</strong>, можете не сомневаться в его исправности и полнейшей безопасности, так \r\n		как наша фирма никогда не допустит, использование транспорта при перевозке хоть с малейшим намеком на какую-либо \r\n		неисправность, итак, Diva предлагает надежные транспортные средства, которые регулярно проходят необходимые тесты и технические \r\n		осмотры. Говоря о тех безопасности, не забываем и о водителе, что будет управлять Вашим транспортным средством. Все наши водители — \r\n		опытные, вежливые, сосредоточенные и грамотные профессионалы высокой квалификации.\r\n			</div>\r\n			<div class="col-12">\r\n				Убедитесь сами, в достоинствах и привилегиях нашего предложения, сделав заказ на нашем сайте лишь заполнив <strong class="under getform point">форму</strong>. Также, можете связаться с нами по указанным на сайте <strong class="under"><a href="http://localhost/jump/contacts/">контактам</a></strong> за дополнительной информацией или введите свой вопрос, кликнув <strong class="under getform point">«Задать вопрос»</strong> и Вам незамедлительно ответит представитель нашей компании.\r\n			</div>\r\n		</div>\r\n	</section>\r\n	\r\n	<!--SERVICES(+)-->\r\n	<section id="services" class="mod container-fluid">\r\n		<h2 class="center">Компания Дива(Diva) предоставляет следующие услуги</h2>\r\n		<section class="icons">\r\n			<div class="col-3">\r\n				<a href="http://localhost/jump/limousines/">\r\n					<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/limo.png" alt="Аренда лимузинов в Одессе, VIP автомобили">\r\n					<h2>Аренда лимузинов в Одессе, VIP автомобили</h2>\r\n				</a>\r\n			</div>\r\n			<div class="col-3">\r\n				<a href="http://localhost/jump/sedans/">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/sedan.png" alt="Аренда седанов">\r\n				<h2>Аренда седанов</h2>\r\n				</a>\r\n			</div>\r\n			<div class="col-3">\r\n				<a href="http://localhost/jump/mikroautobuss/">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/micro.png" alt="Аренда микроавтобусов">\r\n				<h2>Аренда микроавтобусов</h2>\r\n				</a>\r\n			</div>\r\n			<div class="col-3">\r\n				<a href="http://bus-diva.com.ua" target="_blank">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/buss.png" alt="Аренда автобуса Одесса">\r\n				<h2>Аренда автобуса Одесса</h2>\r\n				</a>\r\n			</div>\r\n		</section>\r\n		<section class="icons">\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/planet1.png" alt="Пассажирские перевозки по Одессе, Европе, Украине, странам СНГ">\r\n				<h2>Пассажирские перевозки по Одессе, Европе, Украине, странам СНГ</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/concert.png" alt="концертные туры">\r\n				<h2>концертные туры</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/aeroport.png" alt="Трансферы в аэропорты и вокзалы Украины">\r\n				<h2>Трансферы в аэропорты и вокзалы Украины</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/human-run1.png" alt="Спортивные события">\r\n				<h2>Спортивные события</h2>\r\n			</div>\r\n		</section>\r\n	\r\n		<section>\r\n			<div class="none center">\r\n				<ul class="bull b">\r\n					<li><h2>обслуживание съемочных групп, телеканалов, СМИ</h2></li>\r\n					<li><h2>Детский, школьный, студенческий отдых</h2></li>\r\n					<li><h2>перевозки делегаций</h2></li>\r\n					<li><h2>семинары, тренинги, олимпиады</h2></li>\r\n					<li><h2>паломнические туры по святым местам Украины</h2></li>\r\n					<li><h2>корпоративные путешествия</h2></li>\r\n					<li><h2>выпускные вечера</h2></li>\r\n					<li><h2>индивидуальные заказы по Вашему желанию</h2></li>\r\n				</ul>\r\n				<div class="col-12 content-center">\r\n					<img alt="Транспортная компания Дива, опыт более 20 лет, заказать лимузин, седан, микроавтобус" class="second-logo" src="http://localhost/jump/content/themes/default/img/second-logo.jpg">\r\n				</div>\r\n			</div>\r\n			<div class="col-12 center">\r\n				<button class="button7" id="services">Еще</button>\r\n			</div>\r\n		</section>\r\n	</section>\r\n	\r\n	<!--ADVANTAGES(+)-->\r\n	<section class="attach mod-back ">\r\n		<div class="clearfix myred">\r\n			<h2 class="center">Наши преимущества</h2>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/exp.png" alt="Опыт более 20 лет">\r\n				<h2>Опыт более <span class="under red">20</span> лет</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/quality.png" alt="Высокое качество услуг">\r\n				<h2>Высокое качество услуг</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/time.png" alt="Пунктуальность">\r\n				<h2>Пунктуальность</h2>\r\n			</div>\r\n			<div class="col-3">\r\n				<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/individual.png" alt="Индивидуальный подход к каждому клиенту">\r\n				<h2>Индивидуальный подход к каждому клиенту</h2>\r\n			</div>\r\n		</div>\r\n	</section>\r\n	\r\n	<!--ORDER(+)-->\r\n	<section id="order" class="">\r\n		<div class="col-6 dev-mar-left">\r\n			<h2 class="center">Как воспользоваться нашими услугами?</h2>\r\n			<img class="fl-right mar max5" alt="Оставьте заявку что бы заказать VIP автомобиль в Одессе, лимузин, седан" src="http://localhost/jump/content/themes/default/img/order.png">\r\n			<ol class="padd my" style=" list-style: none;color: #c5d0d4;">\r\n				<li>Выбрать подходящий транспорт или уточнить у менеджера</li>\r\n				<li>Воспользоваться формой заказа или заказать бесплатный звонок</li>\r\n				<li>Указать необходимые данные</li>\r\n				<li>Получить подтверждение и дальнейшие инструкции менеджера нашей компании по телефону и на Вашу электронную почту</li>\r\n			</ol>\r\n		</div>\r\n		<div class="col-6 backright1">\r\n			<div id="order-form">\r\n				<form method="POST">\r\n					<div id="order-title">Форма заказа</div>\r\n					<div id="order-content">\r\n						<input type="text" name="name" id="name" placeholder="Ваше имя">\r\n						<input type="text" name="tel" id="tel" placeholder="Ваш телефон">\r\n						<input type="text" name="mail" id="mail" placeholder="Ваша электронная почта*">\r\n						<textarea name="message" id="message" placeholder="Ваше сообщение"></textarea>\r\n						<button class="button7" id="set-order">Отправить</button>\r\n						<div class="small" style="background: lightgreen; padding: 10px;">\r\n							Вы можете самостоятельно связаться с нами. <br>\r\n							Телефоны: <br>\r\n							+38 (048) 770-27-24,<br> \r\n							+38 (050) 333-48-08<br>\r\n							Почта: 15diva@mail.ru \r\n						</div>\r\n					</div>\r\n				</form>\r\n			</div>\r\n		</div>\r\n	</section>\r\n	\r\n	<!--INTO(-)-->\r\n	<section class="container">\r\n		<h2 class="center indent title">Прокат автобусов в Одессе у компании Дива: взгляд изнутри</h2>\r\n		<article class="col-4 mod">\r\n			<h2 class="h2-blue">Разносторонний опыт</h2>\r\n			<p>\r\n				Большой перечень туристических и морских крюинговых компаний из Одессы, Киева, Днепропетровска, Донецка, Николаева, Крыма … пользовались нашими услугами. Транспортное обеспечение футбольных клубов «Шахтер» (Донецк), «Динамо»(Киев) на играх в Одессе, поездки спортивных команд Одессы на сборы и соревнования, поездки в Киев футбольных болельщиков на матчи Лиги Чемпионов и сборной Украины Не только украинские, но и зарубежные туристические компании предпочитают работать с нашей компанией. Это польская «Атлас Бизнес Сервис», американская туристическая компания «Джоинт», турецкая туристическая компания «Адриатик», транспортное обслуживание крупнейших круизных компаний, суда которых заходят в одесский порт.		\r\n			</p>\r\n		</article>\r\n		<article class="col-4 mod">\r\n			<h2 class="h2-blue">Звездные клиенты</h2>\r\n			<p>\r\n				Большой популярностью пользуются автобусные пассажирские перевозки деятелей искусства, наш транспорт активно используется для гастролей по Украине театров Одессы, а также известных творческих групп и коллективов, таких как группа «ВИА ГРА», Валерий Меладзе, Леонид Якубович, группа «Ляпис Трубецкой», российская телепрограмма «Играй гармонь», Геннадий Ветров, Тото Кутунья, Дмитрий Маликов, группа «Мираж», «Маски-шоу», балет «Тодес», хор Турецкого, группа «Смысловые галлюцинации», известный гитарист Дидюля, группа «Лицей», Юрий Куклачев, актер Валентин Гафт, Клара Новикова, Регина Дубовицкая, Ани Лорак, Михаил Шац, Александр Малинин, Леонид Агутин, группа «Вопли Водоплясова»		\r\n			</p>\r\n		</article>\r\n		<article class="col-4 col-12 clearfix mod">\r\n			<h2 class="h2-blue">Ответственность</h2>\r\n			<p>\r\n				Транспортное обслуживание организаций является одним из приоритетных направлений деятельности нашей транспортной компании. В транспортное обслуживание входит: аренда автобусов и микроавтобусов любого класса для поездок по Одессе и Украине, встречи и проводы (трансфер) в аэропорту и на вокзалах Ваших партнеров, аренда микроавтобусов и автобусов различной вместимости для корпоративных мероприятий и т.д. Наши основные клиенты: ВУЗы, школьные и другие учебные заведения Одессы, лечебные заведения, заводы и фабрики, крупные предприятия города, такие как, Лукойл, Киевстар, UMC, Международная Финансовая Корпорация, Морской Транспортный Банк, АКБ «Пивденный», Кока — Кола, Южный припортовый завод, МЧС, ГНИ, Одесская обладминистрация, известные украинские и российские кинокомпании…		\r\n			</p>\r\n		</article>\r\n	</section>\r\n	\r\n	<!--SLIDER(+)-->\r\n	<h2 id="slidtitle">Наш парк автомобилей</h2>\r\n	<p>\r\n	В наших разделах Вас ждет множество фотографий <strong><a href="http://localhost/jump/limousines/">лимузинов</a> в аренду по Одессе</strong>, <strong>прокат <a href="http://localhost/jump/mikroautobuss/">микроавтобусов</a> по Одессе</strong>, Европе и странам СНГ и <strong>заказ \r\n<a href="http://localhost/jump/mikroautobuss/">авто</a> в Одессе и Украине</strong> для <strong><a href="http://bus-diva.com.ua" target="_blank">пассажирских перевозок</a></strong> подходящий на любой вкус, которые Вы можете как заказать, так и <strong>арендовать или взять \r\nнапрокат</strong>, связавшись с менеджерами нашей компании, которые с радость ответят на все Ваши вопросы и предоставят <strong class="under getform point">бесплатную</strong> консультацию.\r\n	</p>\r\n	<div class="slider container">\r\n		<div class="ss">\r\n			<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/1.jpg" alt="Лимузины транспортной компании в Одессе по доступным ценам"><div class="slider-title"><div>Лимузины</div><div>Широкий выбор лимузинов на любой вкус</div></div></div>\r\n			<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/2.jpg" alt="Лимузин Lexus в Одессе заказать"><div class="slider-title"><div>Lexus</div><div>Мест: 16 </div></div></div>\r\n			<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/3.jpg" alt="Ретро лимузины в городе Одесса, ЗИМ"><div class="slider-title"><div>ЗИМ</div><div>Мест: 6</div></div></div>\r\n			<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/4.jpg" alt="Rolls-Royce в Одессе заказать у транспортной компании Дива"><div class="slider-title"><div>Rolls-Royce седан</div><div>Мест: 8</div></div></div>\r\n			<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/5.jpg" alt="Арендовать микроавтобус в Одессе пассажирские пеервозки, Украина"><div class="slider-title"><div>Микроавтобус Mercedes Sprinter 515</div><div>Мест: 23</div></div></div>\r\n			<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/6.jpg" alt="Наши лимузины, ознакомьтесь с описанием"><div class="slider-title"><div class="title-small">Салоны наших автомобилей</div><div class="title-small">Кожаный салон, TV, DVD, люк, электро подогрев сидений, камера заднего вида, климат-контроль 2х зональный, стеклянный пол, лазерное шоу, дым машина, светомузыка, неоновая подсветка, 5 дверей, 12 метров.</div></div></div>\r\n		</div>\r\n		<div class="controls">\r\n			<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png" alt="промотать назад"></div>\r\n			<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png" alt="промотать вперед"></div>\r\n		</div>\r\n	</div>\r\n	<div class="thumbs">\r\n		<img class="lazy active" src="http://localhost/jump/content/themes/default/img/slider/thumbs/1.jpg" alt="thumbs1">\r\n		<img class="lazy" src="http://localhost/jump/content/themes/default/img/slider/thumbs/2.jpg" alt="thumbs2">\r\n		<img class="lazy" src="http://localhost/jump/content/themes/default/img/slider/thumbs/3.jpg" alt="thumbs3">\r\n		<img class="lazy" src="http://localhost/jump/content/themes/default/img/slider/thumbs/4.jpg" alt="thumbs4">\r\n		<img class="lazy" src="http://localhost/jump/content/themes/default/img/slider/thumbs/5.jpg" alt="thumbs5">\r\n		<img class="lazy" src="http://localhost/jump/content/themes/default/img/slider/thumbs/6.jpg" alt="thumbs6">\r\n	</div>\r\n	\r\n	<!--REVIEWS(+)-->\r\n	<section id="reviews" class="container">\r\n		<h2 class="center indent title">Отзывы наших клиентов</h2>\r\n		<div class="col-12">\r\n			<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/review.jpg" alt="Отзыв для транспортной компании Дива, Екатерина, г. Одесса" />\r\n			<div>\r\n				Благодарим всем коллективом транспортную компанию «Дива» за предоставленные высокопрофессиональные услуги. Качество сервиса и ответственность на высочайшем уровне. Успехов в дальнейшей работе. \r\n				<div class="review-sign">Екатерина, г. Одесса</div>\r\n			</div>\r\n		</div>\r\n		<div class="col-12">\r\n			<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/review2.jpg" alt="Отзыв для транспортной компании Дива, Людмила, Усатово" />\r\n			<div>\r\n				Каждый сезон пользуемся услугами данной компании. Лучшая транспортная компания в Одессе с которой только приходилось работать, заказать автобусы и лимузины в Одессе у компании «Дива» очень просто, остаются лишь великолепные впечатления и этому сопутствует их 20ти летний опыт. Спасибо.\r\n				<div class="review-sign right">Людмила, Усатово</div>\r\n			</div>\r\n		</div>\r\n		<div class="col-12 clearfix">\r\n			<img class="lazy" src="http://localhost/jump/content/themes/default/img/icons/person1.png" alt="Отзыв для транспортной компании Дива, Алина, г. Одесса" />\r\n			<div>\r\n				Спасибо за вчерашний выпускной вечер, прогулка по ночной Одессе всем классом это одно из чудес что приходилось переживать, в лимузинах нашлось все что только нужно, отменный сервис за разумную цену.\r\n				<div class="review-sign">Алина, г. Одесса</div>\r\n			</div>\r\n		</div>\r\n		<div class="col-12 center">\r\n			<div class="title3">Мы будем благодарны если вы оставите нам отзыв о наших услугах что бы другие смогли прочитать</div>\r\n			<button class="button7" id="add-review" onclick="note.get(\'Добавить отзыв\', 2);">Добавить отзыв</button>\r\n		</div>\r\n	</section>', 'Тест', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-10-22 14:00:03', '2017-12-18 08:11:11'),
(2, 'sedans', 'Прокат седанов в Одессе', '<div class="container">\r\n	<div class="max">\r\n		<p>\r\n			Наша компания предоставляет услуги по <strong>аренде седанов с водителем</strong>, можете не волноваться за проведенное время, ведь сообщив нашим водителям место и время вы получаете поездку без риска \r\nопоздания и каких-либо сложностей. По таким направлениям: <strong>Одесса, Украина, страны СНГ и Европы</strong>.\r\n		</p>\r\n		\r\n		\r\n		В седане присутствуют такие удобства как:\r\n		<ul class="orig-ul">\r\n			<li>стильный внешний вид</li>\r\n			<li>изолированный от салона багажник</li>\r\n			<li>относительная универсальность</li>\r\n			<li>автомобили бизнес и представительского класса</li>\r\n			<li>настоящий кожаный салон(эталон стиля)</li>\r\n			<li>вентиляция и подогрев сидений</li>\r\n			<li>4-х зонный климат-контроль</li>\r\n			<li>камера заднего вида.. и многое другое</li>\r\n		</ul>\r\n	</div>\r\n	\r\n	<div class="row marg-b slide-descr">\r\n		<div class="col-6">\r\n			<div class="slider">\r\n				<div class="ss">\r\n					<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/1.jpg" alt="заказать авто на свадьбу в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/2.jpg" alt="свадебное авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/3.jpg" alt="аренда vip авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/4.jpg" alt="аренда авто с водителем одесса"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/5.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/6.jpg" alt="..."></div>\r\n				</div>\r\n				<div class="controls">\r\n					<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png"></div>\r\n					<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png"></div>\r\n				</div>\r\n			</div>\r\n			<div class="thumbs">\r\n				<img class="lazy active" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/1.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/2.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/3.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/4.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/5.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221white/thumb/6.jpg">\r\n			</div>\r\n		</div>\r\n		<div class="col-6">\r\n			<div class="slide-description">\r\n				<div class="s-title">Описание</div>\r\n				<table width="100%">\r\n					<tr>\r\n						<td>Марка:</td>\r\n						<td class="model">Mercedes W221</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Мест:</td>\r\n						<td>3</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Оборудование:</td>\r\n						<td>Кожаный салон — бежевый, электро подогрев и вентиляция сидений, камера заднего вида, климат-контроль 4-х зонный, втяжки дверей. Цвет кузова: белый</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Стоимость:</td>\r\n						<td>Уточняйте пожалуйста с помощью формы связи на сайте или позвоните нам</td>\r\n					</tr>\r\n				</table>\r\n				<div class="button7">Заказать</div>	\r\n			</div>\r\n		</div>\r\n	</div>\r\n	\r\n		<div class="row marg-b slide-descr">\r\n		<div class="col-6">\r\n			<div class="slider">\r\n				<div class="ss">\r\n					<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/1.jpg" alt="прокат авто с водителем одесса"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/2.jpg" alt="заказать авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/3.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/4.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/5.jpg" alt="..."></div>\r\n				</div>\r\n				<div class="controls">\r\n					<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png"></div>\r\n					<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png"></div>\r\n				</div>\r\n			</div>\r\n			<div class="thumbs">\r\n				<img class="lazy active" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/thumb/1.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/thumb/2.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/thumb/3.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/thumb/4.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/221black/thumb/5.jpg">\r\n			</div>\r\n		</div>\r\n		<div class="col-6">\r\n			<div class="slide-description">\r\n				<div class="s-title">Описание</div>\r\n				<table width="100%">\r\n					<tr>\r\n						<td>Марка:</td>\r\n						<td class="model">Mercedes W221</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Мест:</td>\r\n						<td>3</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Оборудование:</td>\r\n						<td>Кожаный салон — бежевый, электро подогрев и вентиляция сидений, камера заднего вида, климат-контроль 4-х зонный, втяжки дверей. Цвет кузова: черный</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Стоимость:</td>\r\n						<td>Уточняйте пожалуйста с помощью формы связи на сайте или позвоните нам</td>\r\n					</tr>\r\n				</table>\r\n				<div class="button7">Заказать</div>	\r\n			</div>\r\n		</div>\r\n	</div>\r\n	\r\n	<div class="row marg-b slide-descr">\r\n		<div class="col-6">\r\n			<div class="slider">\r\n				<div class="ss">\r\n					<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/1.jpg" alt="авто прокат одесса"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/2.jpg" alt="свадебное авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/3.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/4.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/5.jpg" alt="..."></div>\r\n				</div>\r\n				<div class="controls">\r\n					<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png"></div>\r\n					<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png"></div>\r\n				</div>\r\n			</div>\r\n			<div class="thumbs">\r\n				<img class="lazy active" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/thumb/1.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/thumb/2.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/thumb/3.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/thumb/4.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/rols/thumb/5.jpg">\r\n			</div>\r\n		</div>\r\n		<div class="col-6">\r\n			<div class="slide-description">\r\n				<div class="s-title">Описание</div>\r\n				<table width="100%">\r\n					<tr>\r\n						<td>Марка:</td>\r\n						<td class="model">Rolls-Royce</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Мест:</td>\r\n						<td>3</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Оборудование:</td>\r\n						<td>Кожаный салон беж.\\черн. Цвет кузова: бел.\\черн</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Стоимость:</td>\r\n						<td>Уточняйте пожалуйста с помощью формы связи на сайте или позвоните нам</td>\r\n					</tr>\r\n				</table>\r\n				<div class="button7">Заказать</div>	\r\n			</div>\r\n		</div>\r\n	</div>\r\n	\r\n	<div class="row marg-b slide-descr">\r\n		<div class="col-6">\r\n			<div class="slider">\r\n				<div class="ss">\r\n					<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/1.jpg" alt="заказ авто одесса"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/2.jpg" alt="арендовать авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/3.jpg" alt="..."></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/4.jpg" alt="..."></div>\r\n				</div>\r\n				<div class="controls">\r\n					<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png"></div>\r\n					<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png"></div>\r\n				</div>\r\n			</div>\r\n			<div class="thumbs">\r\n				<img class="lazy active" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/thumb/1.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/thumb/2.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/thumb/3.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai/thumb/4.jpg">\r\n			</div>\r\n		</div>\r\n		<div class="col-6">\r\n			<div class="slide-description">\r\n				<div class="s-title">Описание</div>\r\n				<table width="100%">\r\n					<tr>\r\n						<td>Марка:</td>\r\n						<td class="model">Hyundai Sonata YF</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Мест:</td>\r\n						<td>3</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Оборудование:</td>\r\n						<td>Кожаный салон беж.\\черн. Цвет кузова: бел.\\черн</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Стоимость:</td>\r\n						<td>Уточняйте пожалуйста с помощью формы связи на сайте или позвоните нам</td>\r\n					</tr>\r\n				</table>\r\n				<div class="button7">Заказать</div>	\r\n			</div>\r\n		</div>\r\n	</div>\r\n	\r\n	<div class="row marg-b slide-descr">\r\n		<div class="col-6">\r\n			<div class="slider">\r\n				<div class="ss">\r\n					<div class="item active"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/1.jpg" alt="заказ авто одесса"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/2.jpg" alt="арендовать авто в одессе"></div>\r\n					<div class="item"><img src="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/3.jpg" alt="..."></div>\r\n				</div>\r\n				<div class="controls">\r\n					<div class="arr-left" onclick="slider.next(\'right\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-left.png"></div>\r\n					<div class="arr-right" onclick="slider.next(\'left\', this)"><img src="http://localhost/jump/content/themes/default/img/arr-right.png"></div>\r\n				</div>\r\n			</div>\r\n			<div class="thumbs">\r\n				<img class="lazy active" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/thumb/1.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/thumb/2.jpg">\r\n				<img class="lazy" data-original="http://localhost/jump/content/themes/default/img/slider/autos/sedan/Hyundai1/thumb/3.jpg">\r\n			</div>\r\n		</div>\r\n		<div class="col-6">\r\n			<div class="slide-description">\r\n				<div class="s-title">Описание</div>\r\n				<table width="100%">\r\n					<tr>\r\n						<td>Марка:</td>\r\n						<td class="model">Hyundai Grandeur HG</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Мест:</td>\r\n						<td>3</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Оборудование:</td>\r\n						<td>Кожаный салон беж.\\черн. Цвет кузова: бел.\\черн</td>\r\n					</tr>\r\n					<tr>\r\n						<td>Стоимость:</td>\r\n						<td>Уточняйте пожалуйста с помощью формы связи на сайте или позвоните нам</td>\r\n					</tr>\r\n				</table>\r\n				<div class="button7">Заказать</div>	\r\n			</div>\r\n		</div>\r\n	</div>\r\n	\r\n	<div class="max">\r\n		<p>\r\n			На прекрасной свадьбе, эффектном дне рождения, блестящей вечеринке или корпоративе, изумительной выписке из роддома, экскурсии по ночному городу, требует яркости эмоций и впечатлений, \r\nгде выбор автомобиля в одном из первых пунктов.\r\nМы поможем вам сделать оптимальный выбор в соответствии с вашими пожеланиями.\r\nЗаказав у нас прокат седана, мы Вам гарантируем отличное начало и достойное завершение Вашего путешествия и желаем Вам, чтобы оно прошло приятно вместе с нами!\r\nПодача седана по указанному адресу произойдёт точно и быстро.\r\nСеданы в аренду и прокат, представленные нашей компании никак не смогут оставить Вас равнодушными, более того, Вы можете быть совершенно уверенны, что они способны доставить Вам \r\nдостойные впечатления.\r\n\r\n		</p>\r\n		\r\n		<p>\r\n			Встреча и проводы в аэропортах, мор-вокзалах и вокзалах города Одессы(трансфер) нашей компании даст Вам надежду на благополучное решение в Вашем вопросе.\r\nТакже, Вы можете провести бизнес-встречу в стильной и спокойной обстановке.\r\nАренда авто в Одессе — является нашей основной специализацией, ведь обращаясь к нам, Вы получаете высокое качество сервиса.\r\nАрендовать авто на свадьбу - очень важный выбор, от которого многое зависит, ведь именно в этот день свадебный кортеж будет сопровождать молодожен к ЗАГСу, на \r\nфото-сессию, на банкет и на протяжении всего дня сопутствовать хорошему настроению.\r\nОгромный автопарк позволяет обслуживать как частные заказы на аренду авто по Одессе, так и крупные делегации, торжественные мероприятия и прочие заказы \r\nразличного объема и сложности по всей Украине и Европе.\r\n		</p>\r\n		\r\n		<p>\r\n			Все машины находятся в безупречном состоянии снаружи и внутри, что и самый нерешительный клиент сможет выбрать себе авто по вкусу и статусу от нашего автопарка.\r\nТарифы поездок по городу и за его пределами различны их стоимость уточняйте при заказе аренды седана, минимальные затраты мы гарантируем! Хотя можете убедиться, что аренда седана \r\nнамного выгоднее, постоянного использования услуг такси.\r\n		</p>\r\n		\r\n		<p>\r\n			Ознакомиться с выбором предоставляемых нами в авто-прокат и их ценами, Вы можете в разделах интересующих Вас моделей. \r\nУ Diva имеет в наличии для Вас большой выбор современных авто прокат в Одессе по отличным ценам.\r\nДля Вашего удобства при выборе аренды седана в Одессе на 9 торжество, деловые встречи, в аэропорт или на вечеринку, мы создали удобные таблицы с необходимыми \r\nданными.\r\n\r\n		</p>\r\n	</div>\r\n</div>', 'sedans', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-10-22 14:00:03', '2017-12-18 08:11:51'),
(15, 'asaaas', 'asaaas', 'as1', '', 'educator', 0, 0, 'publish', 'closed', 0, 0, '2017-12-06 17:42:42', '2017-12-14 15:29:22'),
(4, 'novosti-kompanii-diva', 'Новости компании Дива', '11Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab, laborum, hic, quam libero beatae deserunt velit cumque explicabo aliquam at rerum vel obcaecati quae maiores consectetur distinctio atque. Nostrum, sequi.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab, laborum, hic, quam libero beatae deserunt velit cumque explicabo aliquam at rerum vel obcaecati quae maiores consectetur distinctio atque. Nostrum, sequi.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab, laborum, hic, quam libero beatae deserunt velit cumque explicabo aliquam at rerum vel obcaecati quae maiores consectetur distinctio atque. Nostrum, sequi.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab, laborum, hic, quam libero beatae deserunt velit cumque explicabo aliquam at rerum vel obcaecati quae maiores consectetur distinctio atque. Nostrum, sequi.', 'Лимузины', 'new', 3, 0, 'publish', 'closed', 0, 0, '2017-11-18 14:00:03', '2017-12-04 15:58:11'),
(33, 'vsem-privet', 'Всем привет', '111zz', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-16 17:06:50', '2017-12-16 15:17:22'),
(7, 'prepodavateli-1', 'Преподаватель - 1', 'aqaq44111111dd', 'Лимузины, фф1', 'educator', 0, 0, 'publish', 'closed', 0, 0, '2017-11-18 14:00:03', '2017-12-04 15:55:14'),
(9, 'uiaыва', 'ыа', 'ыва', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-03 19:40:15', '0000-00-00 00:00:00'),
(10, 'vvvv', 'вввв', 'аа', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-03 19:52:00', '0000-00-00 00:00:00'),
(11, 'aaczui', 'аацы', 'а', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-03 19:52:12', '0000-00-00 00:00:00'),
(12, 'yayaya', 'яяя', 'фф11', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-03 19:52:21', '2017-12-04 15:53:32'),
(13, 'limousines', 'Заказать лимузин в Одессе. Прокат автомобилей', 'hello people', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-06 17:10:32', '0000-00-00 00:00:00'),
(14, 'aaa', 'aaa', '11', '', 'page', 0, 0, 'publish', 'closed', 0, 0, '2017-12-06 17:37:35', '2017-12-06 15:37:42'),
(22, 'kjk', 'kjk', 'ii', '', 'educator', 0, 0, 'publish', 'closed', 0, 0, '2017-12-08 18:50:11', '2017-12-16 10:50:31'),
(32, 'aa', 'aa', '1', '', 'new', 0, 0, 'publish', 'closed', 0, 0, '2017-12-14 19:06:08', '2017-12-14 17:06:19');

-- --------------------------------------------------------

--
-- Структура таблицы `terms`
--

CREATE TABLE `terms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `term_group` bigint(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `terms`
--

INSERT INTO `terms` (`id`, `name`, `slug`, `term_group`) VALUES
(1, 'hip-hop122', 'hip-hop22', 0),
(2, 'hip-hop1', 'hip-hop1', 0),
(15, '1s3', '1s3', 0),
(37, '1111', '1111', 0),
(13, '11', '11', 0),
(12, '122', '122', 0),
(11, '12', '12', 0),
(10, '11', '11', 0),
(16, '45gf', '45gf', 0),
(24, '11a', '11a', 0),
(19, 'League', 'League', 0),
(36, 'hip-hop2', 'hip-hop2', 0),
(21, 'test', 'test', 0),
(32, 'qaz', 'qaz', 0),
(33, 'asd', 'asd', 0),
(34, 'yy', 'yy', 0),
(38, 'hip-hop3', 'samb', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `term_relationships`
--

CREATE TABLE `term_relationships` (
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL,
  `term_order` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `term_relationships`
--

INSERT INTO `term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES
(7, 1, 0),
(15, 1, 0),
(22, 15, 0),
(15, 36, 0),
(32, 21, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `term_taxonomy`
--

CREATE TABLE `term_taxonomy` (
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `taxonomy` varchar(32) NOT NULL,
  `description` longtext NOT NULL,
  `parent` bigint(20) UNSIGNED NOT NULL,
  `count` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `term_taxonomy`
--

INSERT INTO `term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'educator-cat', 'test111', 0, 2),
(2, 2, 'educator-tag', '', 0, 1),
(15, 15, 'educator-cat', '', 0, 1),
(14, 14, 'educator-tag', '', 0, 0),
(13, 13, 'educator-tag', '', 0, 0),
(12, 12, 'educator-cat', '', 0, 0),
(11, 11, 'educator-cat', '', 0, 1),
(10, 10, 'educator-cat', '', 0, 0),
(16, 16, 'educator-cat', '', 0, 0),
(24, 24, 'new-tag', '', 0, 0),
(19, 19, 'new-cat', '', 0, 0),
(21, 21, 'new-tag', '', 0, 1),
(38, 38, 'educator-style', '', 0, 0),
(36, 36, 'educator-style', '', 0, 0),
(32, 32, 'new-tag', '', 0, 0),
(33, 33, 'new-tag', '', 0, 0),
(34, 34, 'new-tag', '', 0, 0),
(37, 37, 'educator-tag', '', 0, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `postmeta`
--
ALTER TABLE `postmeta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tags` (`tags`),
  ADD KEY `status` (`status`),
  ADD KEY `parent` (`parent`),
  ADD KEY `autor` (`autor`),
  ADD KEY `url` (`url`,`post_type`) USING BTREE;

--
-- Индексы таблицы `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `slug` (`slug`);

--
-- Индексы таблицы `term_relationships`
--
ALTER TABLE `term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Индексы таблицы `term_taxonomy`
--
ALTER TABLE `term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id` (`term_id`,`taxonomy`),
  ADD KEY `taxonomy` (`taxonomy`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `postmeta`
--
ALTER TABLE `postmeta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT для таблицы `terms`
--
ALTER TABLE `terms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT для таблицы `term_taxonomy`
--
ALTER TABLE `term_taxonomy`
  MODIFY `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

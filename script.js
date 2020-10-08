let arrLang = {
	'en' : {
		'Политика конфиденциальности' : 'Privacy policy',
		'Авторские права' : 'Copyright',
		'Контакты' : 'Contacts',
		'Все права защищены Laikacolor.com' : 'All rights reserved Laikacolor.com',
		'Дизайнеры' : 'Designers',
		'Художники' : 'Painter',
		'Партнеры' : 'Partners',
		'Сообщества' : 'Communities'
	},
	'ru' : {
		'Политика конфиденциальности' : 'Политика конфиденциальности',
		'Авторские права' : 'Авторские права',
		'Контакты' : 'Контакты',
		'Все права защищены Laikacolor.com' : 'Все права защищены Laikacolor.com',
		'Дизайнеры' : 'Дизайнеры',
		'Художники' : 'Художники',
		'Партнеры' : 'Партнеры',
		'Сообщества' : 'Сообщества'
	},
};

$(function(){
	$('.translate').click(function(){
		let lang = $(this).attr('id');
		$('.lang').each(function(index, element){
			$(this).text(arrLang[lang][$(this).attr('key')]);
		})
	})
})
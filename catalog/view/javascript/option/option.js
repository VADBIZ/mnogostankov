function uploadOptionFile(id) {
	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
		clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$('#button-upload' + id).button('loading');
				},
				complete: function() {
					$('#button-upload' + id).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$('#input-option' + id).after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);
						$('#input-option' + id).val(json['code']);
					} 
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
}

function priceFormat(price) { 
	format = $('input[name=\'price_format\']');
    decimals = format.data('decimals');
    decimal_point = format.data('decimalPoint');
    thousand_point = format.data('thousandPoint');
	symbol_left = format.data('symbolLeft');
	symbol_right = format.data('symbolRight'); 
	
    price = price * format.data('value');

    price_abs = '' + parseInt(price = Math.abs(price).toFixed(decimals)); 
    price_length = ((price_length = price_abs.length) > 3) ? price_length % 3 : 0;

    price_format = (price_length ? price_abs.substr(0, price_length) + thousand_point : '') + price_abs.substr(price_length).replace(/(\d{3})(?=\d)/g, "$1" + thousand_point) + (decimals ? decimal_point + Math.abs(price - price_abs).toFixed(decimals).slice(2) : ''); 
	
	price_format = symbol_left + price_format  + symbol_right;
	
	return price_format;
}

function updatePrice(id) {
	var product = '#product' + id;
	
	if (typeof $('.calc-price' + id).data('price') != 'undefined') {
		var price = Number($('.calc-price' + id).data('price'));
	} else {
		var price = false;
	}
	
	if (typeof $('.calc-special' + id).data('special') != 'undefined') {
		var special = Number($('.calc-special' + id).data('special'));
	} else {
		var special = false;
	}
	
	if (typeof $('.calc-tax' + id).data('tax') != 'undefined') {
		var tax = Number($('.calc-tax' + id).data('tax'));
	} else {
		var tax = false;
	}
	
	if (typeof $('.calc-points' + id).data('points') != 'undefined') {
		var points = Number($('.calc-points' + id).data('points'));
	} else {
		var points = false;
	}
	
	var quantity = Number($(product + ' input[name=\'quantity\']').val());
	
	$(product + ' input:checked, ' + product + ' option:selected').each(function() {
		
		if (price !== false) {
		
			if ($(this).data('price-prefix') == '+') {
				price = price + Number($(this).data('price'));
				
				if (special) {
					if (typeof $(this).data('special') != 'undefined') {
						special = special + Number($(this).data('special'));
					} else {
						special = special + Number($(this).data('price'));
					}
				}
				
				if (tax && typeof $(this).data('tax') != 'undefined') {
					tax = tax + Number($(this).data('tax'));
				}
			} else {
				if ($(this).data('price-prefix') == '-') {
					price = price - Number($(this).data('price'));
					
					if (special) {
						if (typeof $(this).data('special') != 'undefined') {
							special = special - Number($(this).data('special'));
						} else {
							special = special - Number($(this).data('price'));
						}
					}
					
					if (tax && typeof $(this).data('tax') != 'undefined') {
						tax = tax - Number($(this).data('tax'));
					}
				} else {
					if ($(this).data('price-prefix') == '=') {
						price = Number($(this).data('price'));
						
						if (special) {
							if (typeof $(this).data('special') != 'undefined') {
								special = Number($(this).data('special'));
							} else {
								special = Number($(this).data('price'));
							}
						}
						
						if (tax && typeof $(this).data('tax') != 'undefined') {
							tax = Number($(this).data('tax'));
						}
					} else {
						if ($(this).data('price-prefix') == '*') {
							price = price * Number($(this).data('price'));
							
							if (special) {
								if (typeof $(this).data('special') != 'undefined') {
									special = special * Number($(this).data('special'));
								} else {
									special = special * Number($(this).data('price'));
								}
							}
							
							if (tax && typeof $(this).data('tax') != 'undefined') {
								tax = tax * Number($(this).data('tax'));
							}
						} else {
							if ($(this).data('price-prefix') == '/') {
								price = price / Number($(this).data('price'));
								
								if (special) {
									if (typeof $(this).data('special') != 'undefined') {
										special = special / Number($(this).data('special'));
									} else {
										special = special / Number($(this).data('price'));
									}
								}
								
								if (tax && typeof $(this).data('tax') != 'undefined') {
									tax = tax / Number($(this).data('tax'));
								}
							} else {
								if ($(this).data('price-prefix') == '%') {
									price = price + (Number($(this).data('price') * (price / 100)));
									
									if (special) {
										if (typeof $(this).data('special') != 'undefined') {
											special = special + (Number($(this).data('special') * (special / 100)));
										} else {
											special = special + (Number($(this).data('price') * (special / 100)));
										}
									}
									
									if (tax && typeof $(this).data('tax') != 'undefined') {
										tax = tax + (Number($(this).data('tax') * (tax / 100)));
									}
								} else {
									if ($(this).data('price-prefix') == 'p') {
										price = price - (Number($(this).data('price') * (price / 100)));
										
										if (special) {
											if (typeof $(this).data('special') != 'undefined') {
												special = special - (Number($(this).data('special') * (special / 100)));
											} else {
												special = special - (Number($(this).data('price') * (special / 100)));
											}
										}
										
										if (tax && typeof $(this).data('tax') != 'undefined') {
											tax = tax - (Number($(this).data('tax') * (tax / 100)));
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		if (points) {
			if ($(this).data('points-prefix') == '+') {
				points = (points + Number($(this).data('points')) * quantity);
			}
			
			if ($(this).data('points-prefix') == '-') {
				points = (points - Number($(this).data('points')) * quantity);
			}
		}
    });
	
	$('.calc-price' + id).html(priceFormat(price * quantity));
	$('.calc-special' + id).html(priceFormat(special * quantity));
	$('.calc-tax' + id).html(priceFormat(tax * quantity));
	
	if (points) {
		$('.calc-points' + id).html(points);
	}
}

function addCartOption(id, minimum) {
	var product = '#product' + id;
	
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $(product + ' input[type=\'text\'], ' + product + ' input[type=\'hidden\'], ' + product + ' input[type=\'radio\']:checked, ' + product + ' input[type=\'checkbox\']:checked, ' + product + ' select, ' + product + ' textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#cart > button').button('loading');
		},
		complete: function() {
			$('#cart > button').button('reset');
		},
		success: function(json) {
			$('.alert, .alert-dismissible, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				if (json['error']['option']) {
					
					for (i in json['error']['option']) {
						var element = $('#input-option' + id + i.replace('_', '-'));
						
						if (!$(element).length && json['redirect']) {
							location = json['redirect'];	
						}

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}
		
			if (json['success']) {
				$('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				$('html, body').animate({ scrollTop: 0 }, 'slow');

				$('#cart > ul').load('index.php?route=common/cart/info ul li');
				
				// Reset quantity
				$(product + ' input[name=\'quantity\']').val(Number($(product + ' input[name=\'quantity\']').data('minimum')));
			}
		}
    });
}

function updateQuantity(prefix, minimum, id) {
	var product = '#product' + id;
	var input = $(product + ' input[name=\'quantity\']');
	
	var quantity = parseInt($(input).val(), 10);

	if (prefix == '+') {
		quantity++;
	} else {
		if (quantity > parseInt(minimum, 10)) {
			quantity--;
		}
	}
	
	$(input).val(quantity);
	updatePrice(id);
}
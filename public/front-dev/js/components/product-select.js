/**
 * 2007-2019 egio and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@egio.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade egio to newer
 * versions in the future. If you wish to customize egio for your
 * needs please refer to https://www.egio.com for more information.
 *
 * @author    egio SA <contact@egio.com>
 * @copyright 2007-2019 egio SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of egio SA
 */
import $ from 'jquery';
import 'velocity-animate';

export default class ProductSelect {
  init() {
    const MAX_THUMBS = 5;
    const FLAG_MARGIN = 10;
    let $arrows =   $('.js-modal-arrows');
    let $thumbnails = $('.js-modal-product-images');
    let $onsale =  $('.on-sale');
    let $pack = $('.pack');
    let $new = $('.new');

    $('body').on('click','.js-modal-thumb', (event) => {
      if($('.js-modal-thumb').hasClass('selected')){
        $('.js-modal-thumb').removeClass('selected');
      }
      $(event.currentTarget).addClass('selected');
      $('.js-modal-product-cover').attr('src', $(event.target).data('image-large-src'));
      $('.js-modal-product-cover').attr('title', $(event.target).attr('title'));
      $('.js-modal-product-cover').attr('alt', $(event.target).attr('alt'));
    })
    .on('click', 'aside#thumbnails', (event) => {
      if (event.target.id == 'thumbnails'){
        $('#product-modal').modal('hide');
      }
    });

    if ($onsale.length && $('#product').length) {
      $new.css('top', $onsale.height() + 1 * FLAG_MARGIN);
      $pack.css('top', $onsale.height() + 1 * FLAG_MARGIN);
    }

    if ($new.length && $('#product').length) {
      $pack.css('top', $onsale.height() + 5 * FLAG_MARGIN);
    }

    if ($('.js-modal-product-images li').length <= MAX_THUMBS) {
      $arrows.css('opacity', '.2');
    } else {
      $arrows.on('click', (event) => {
        if ($(event.target).hasClass('arrow-up') && $thumbnails.position().top < 0) {
          this.move('up');
          $('.js-modal-arrow-down').css('opacity','1');
        } else if ($(event.target).hasClass('arrow-down') && $thumbnails.position().top + $thumbnails.height() >  $('.js-modal-mask').height()) {
          this.move('down');
          $('.js-modal-arrow-up').css('opacity','1');
        }
      });
    }
  }

  move(direction) {
    const THUMB_MARGIN = 10;
    var $thumbnails = $('.js-modal-product-images');
    var thumbHeight = $('.js-modal-product-images li img').height() + THUMB_MARGIN;
    var currentPosition = $thumbnails.position().top;
    $thumbnails.velocity({
      translateY: (direction === 'up') ? currentPosition + thumbHeight : currentPosition - thumbHeight
    },function(){
      if ($thumbnails.position().top >= 0) {
        $('.js-modal-arrow-up').css('opacity','.2');
      } else if ($thumbnails.position().top + $thumbnails.height() <=  $('.js-modal-mask').height()) {
        $('.js-modal-arrow-down').css('opacity','.2');
      }
    });
  }
}
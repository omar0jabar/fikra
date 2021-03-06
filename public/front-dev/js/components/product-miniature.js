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

export default class ProductMinitature {
  init() {
    $('.js-product-miniature').each((index, element) => {
      const FLAG_MARGIN = 10;
      const discountElems = $(element).find('.discount-product');
      const onSaleElems =  $(element).find('.on-sale');
      const newElems = $(element).find('.new');

      if (discountElems.length) {
        newElems.css('top', discountElems.height() * 3 + FLAG_MARGIN);
        discountElems.css('top', -$(element).find('.thumbnail-container').height() + $(element).find('.product-description').height() + FLAG_MARGIN);

        if ($(element).find('.pack').length) {
          $(element).find('.pack').css('top', discountElems.height() * 3 + FLAG_MARGIN);
        }
      }

      if (onSaleElems.length) {
        discountElems.css('top', parseFloat(discountElems.css('top')) + onSaleElems.height() + FLAG_MARGIN);
        newElems.css('top', (discountElems.height() * 3 + onSaleElems.height()) + FLAG_MARGIN * 3);
      }

      if ($(element).find('.color').length > 5) {
        let count = 0;

        $(element).find('.color').each((index, element) => {
          if (index > 4) {
            $(element).hide();
            count ++;
          }
        });

        $(element).find('.js-count').append(`+${count}`);
      }
    });
  }
}

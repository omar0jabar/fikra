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

import './lib/underscore';

import 'expose-loader?Tether!tether';
import 'bootstrap/dist/js/bootstrap.min';
import 'flexibility';
import 'bootstrap-touchspin';
import 'bootstrap-input-spinner';
import 'jquery-validation'; 

import './sliders';
 
import './lib/bootstrap-filestyle.min';

import './lib/jquery.carousel';


import './lib/iframe_api';
  


jQuery(function ($) { 
  var MYplayer;
  var Player = (function () { 
    //private static
    var defaults = {
      events: {},
      playerVars: {
        modestbranding: 0,
        controls: 1, //remove controls
        showinfo: 0,
        enablejsapi: 1,
        iv_load_policy: 3
      }
    };
 
    var constructor = function (options) {
      this.options = $.extend(defaults, options);

      if (this.options.autoPlay) {
        this.options.events['onReady'] = function (event) {
          event.target.playVideo()
        }
      }
      this.player = new YT.Player(this.options.id, this.options);
      MYplayer = this.player;
    }

    return constructor;
  })() //function(){
  $(document).ready(function () {

    $('#videoIframe').click(function () {
      var ytbId = $(this).find('img').fadeOut(500).attr('data-videoId');
      $(this).addClass("p-iframe");
      myPlayer = new Player({
        id: 'videoIframeInner',
        changeVideo: '.videoGal',
        autoPlay: true,
        videoId: ytbId,
        playerVars: {rel: 0, showinfo: 0, ecver: 2}
      });
    });

  });
});

$(document).ready(() => {


  $(".navbar-toggler").click(function(){
    $(this).toggleClass("is-open");
    $('body').toggleClass('overflow');
  });

  if ($('.search input[type="text"]').length) {
    if ($('.search input[type="text"]').val() != '') {
      $('.search').addClass('has-data');
    }
  }

  $('.search input[type="text"]').focusout(function () {
    if ($(this).val() != '') {
      $(this).parent().addClass('has-data');
    } else {
      $(this).parent().removeClass('has-data');
    }
  });

  // Select 2 intialize
  $(".select2").select2({
    placeholder: "Année",
    minimumResultsForSearch: -1
  });
  var count = $( ".CarouselDots li" ).length;
  if ( count == 1 ) 
  {
    $( ".CarouselDots li" ).hide();
  }

  // Slider HP
    var sliderHp = $('.slider-hp');
  if (sliderHp.length) {
    sliderHp.slick({
        dots: true,
        arrows: false,
        speed: 400,
        autoplay: true,
        autoplaySpeed: 10000,
        infinite: true,
        fade: true,
        pauseOnHover:false,
        slidesToShow: 1,
        slidesToScroll: 1,
    });

    $(".slider-hp").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $(".slider-hp .slick-dots li").removeClass("slick-active");
      $(".slider-hp .slick-dots li button").attr("aria-pressed","false").focus(function() {
            this.blur();
        });
    });
  }

  // wrap table
    $( "table" ).wrap( "<div class='overflow-x-auto'></div>" );

  // Slider Tabs Page Projects
  $('.block-tabs #myTab').slick({
    dots: false,
    arrows: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    variableWidth: true,
    infinite: false,
    responsive: [
      {
        breakpoint: 1025,
        settings: {
          // variableWidth: true,
          // infinite: false,
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 992,
        settings: {
          // variableWidth: true,
          // infinite: false,
          slidesToShow: 2,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 641,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
    ]
  });

  if ($(window).width() < 1026) {
    $('.nav-item.parent .nav-link').click(function() {
      $(this).toggleClass('show').next('.sous-title').slideToggle(400);
        $(this).parent('.nav-item').siblings().find('.nav-link').removeClass('show');
      $(this).parent('.nav-item').siblings().find('ul.sous-title').slideUp(400);
    });
  }

  if ($(".b-project-content.project").length){
      var CalculateContainer = function(){

        var $paddingLeft = $('.b-project-content .container').offset().left;
        var $containerleftBlock = $('.sidebar').width();
        var $LeftColumnwidth = $paddingLeft + $containerleftBlock+15;
        $('.banner-project .project-info').css('padding-left',$paddingLeft+15);
        $('.banner-project .block-card').css('position','absolute');
        $('.banner-project .block-card').css('right','0');
        $('.banner-project .block-card').css('max-width',$LeftColumnwidth);
        $('.block-img').css('max-width', $(window).width() - $LeftColumnwidth);
      };

      if ($(window).width() > 1400) {
          CalculateContainer();
          $(window).resize(function(){
            setTimeout(CalculateContainer, 300);
          })
      }
  }
  // add class padding-bottom (pb-200)
  $('.block-blue1, .block-blue2').parent().parent().addClass('bg-color');

  // $('#show-info').click(function(e) {
  //     e.preventDefault();
  //     $(this).toggleClass("active");
  //     $('.c-block-links .c-info').toggle();
  // });

  // show and hide go to top button
  $(window).scroll(function() {
    if ($(this).scrollTop() > 500) {
      $('#back-to-top').show();
    } else {
      $('#back-to-top').hide();
    }
  });

  // slider projets recents HP
  $('.slider-projects').slick({
    dots: false,
    arrows: true,
    speed: 300,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 641,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          dots: true
        }
      },
    ]
  });

  // slider choose flowingo
  $('.slider').slick({
    dots: false,
    arrows: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    centerMode: true,
    responsive: [
        {
        breakpoint: 767,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            }
        },
    ]
  });
  

  // Animation Scroll To Element 
  $('a[href^="#"]').on('click', function(event) {
    var target = $(this.getAttribute('href'));
    if( target.length ) {
        event.preventDefault();
        $('html, body').stop().animate({
            scrollTop: target.offset().top - $('header').innerHeight()
        }, 1000);
    }
  });

  // Animation Scroll To Element Page Project
  $('.navigation-bar .navigation-links ul li a[href^="#"]:not([href="#"])').click(function () {
    var target = $(this.hash);
    target = target.length ? target : $('[name=' + this.hash.substr(1) + ']');
    if (target.length) {
      if ($(window).width() < 1025) {
        // Add/Remove Class On Click For Fiche Project Navigation Bar
        $(this).parent('li').addClass('active').siblings().removeClass('active');
      }
      if ($(window).width() < 1025) {
        $('html,body').animate({
          scrollTop: target.offset().top - $('header').innerHeight() - 10
        }, 800);
      } else {
        $('html,body').animate({
          scrollTop: target.offset().top - $('header').innerHeight()
        }, 800);
      }
      return false;
    }
  }); 

  if ($(window).width() > 1024) {
    if ($('.b-project-content').length) {
      $(window).scroll(function() { 
        //syncronize navbar && fixed-menu && footer links with sections
        $('.b-project-content .section').each(function() {
          if ($(window).scrollTop() >= $(this).offset().top - $('header').innerHeight() - $('.navigation-bar').innerHeight() - 16) {
            var sectionID	= $(this).attr('id');
            $('.navigation-bar .navigation-links ul li').removeClass('active');
    
            $('.navigation-bar .navigation-links ul li[data-scroll="'+ sectionID +'"]').addClass('active');
          }
        });
      });
    };
  };

  var CurrentLang = $('html').attr('lang');
  if (CurrentLang=='fr') {
      $.extend( $.validator.messages, {
      required: "Ce champ est obligatoire.",
      remote: "Veuillez corriger ce champ.",
      email: "Veuillez fournir une adresse électronique valide.",
      url: "Veuillez fournir une adresse URL valide.",
      date: "Veuillez fournir une date valide.",
      dateISO: "Veuillez fournir une date valide (ISO).",
      number: "Veuillez fournir un numéro valide.",
      digits: "Veuillez fournir seulement des chiffres.",
      creditcard: "Veuillez fournir un numéro de carte de crédit valide.",
      equalTo: "Veuillez fournir encore la même valeur.",
      notEqualTo: "Veuillez fournir une valeur différente, les valeurs ne doivent pas être identiques.",
      extension: "Veuillez fournir une valeur avec une extension valide.",
      maxlength: $.validator.format( "Veuillez fournir au plus {0} caractères." ),
      minlength: $.validator.format( "Veuillez fournir au moins {0} caractères." ),
      rangelength: $.validator.format( "Veuillez fournir une valeur qui contient entre {0} et {1} caractères." ),
      range: $.validator.format( "Veuillez fournir une valeur entre {0} et {1}." ),
      max: $.validator.format( "Veuillez fournir une valeur inférieure ou égale à {0}." ),
      min: $.validator.format( "Veuillez fournir une valeur supérieure ou égale à {0}." ),
      step: $.validator.format( "Veuillez fournir une valeur multiple de {0}." ),
      maxWords: $.validator.format( "Veuillez fournir au plus {0} mots." ),
      minWords: $.validator.format( "Veuillez fournir au moins {0} mots." ),
      rangeWords: $.validator.format( "Veuillez fournir entre {0} et {1} mots." ),
      letterswithbasicpunc: "Veuillez fournir seulement des lettres et des signes de ponctuation.",
      alphanumeric: "Veuillez fournir seulement des lettres, nombres, espaces et soulignages.",
      lettersonly: "Veuillez fournir seulement des lettres.",
      nowhitespace: "Veuillez ne pas inscrire d'espaces blancs.",
      ziprange: "Veuillez fournir un code postal entre 902xx-xxxx et 905-xx-xxxx.",
      integer: "Veuillez fournir un nombre non décimal qui est positif ou négatif.",
      vinUS: "Veuillez fournir un numéro d'identification du véhicule (VIN).",
      dateITA: "Veuillez fournir une date valide.",
      time: "Veuillez fournir une heure valide entre 00:00 et 23:59.",
      phoneUS: "Veuillez fournir un numéro de téléphone valide.",
      phoneUK: "Veuillez fournir un numéro de téléphone valide.",
      mobileUK: "Veuillez fournir un numéro de téléphone mobile valide.",
      strippedminlength: $.validator.format( "Veuillez fournir au moins {0} caractères." ),
      email2: "Veuillez fournir une adresse électronique valide.",
      url2: "Veuillez fournir une adresse URL valide.",
      creditcardtypes: "Veuillez fournir un numéro de carte de crédit valide.",
      ipv4: "Veuillez fournir une adresse IP v4 valide.",
      ipv6: "Veuillez fournir une adresse IP v6 valide.",
      require_from_group: $.validator.format( "Veuillez fournir au moins {0} de ces champs." ),
      nifES: "Veuillez fournir un numéro NIF valide.",
      nieES: "Veuillez fournir un numéro NIE valide.",
      cifES: "Veuillez fournir un numéro CIF valide.",
      postalCodeCA: "Veuillez fournir un code postal valide."
    } );
    }

  $.validator.addMethod('customphone', function (value, element) {
      return /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{6,7}?$/.test(value);
  },$.validator.messages.number);
    $.validator.addMethod('customemail', function (value, element) {
        var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
        return re.test(value);
    },$.validator.messages.email);
    // pour chaque formulaire mettre une validation pour les champs obligatoirs
    $('.b-project-content form').each(function(){
      $(this).validate({
        submitHandler: function(form) {
          form.submit();
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
      });
    });
    $('#form-newsletter').validate({
        rules: {
            'email-newsletter': {
                required: true,
                customemail: true
            }
        },
        submitHandler : function(form) {
            //do something here
            // form.submit();
        }
    });
    $('#form-newsletter-docs').validate({
        rules: {
            'email-newsletter-docs': {
                required: true,
                customemail: true
            },
            'type': {
                required: true
            }
        },
        submitHandler : function(form) {
            //do something here
            // form.submit();
        }
    });
    $('form[name="accueil-contact"]').validate({
        rules: {
            'email': {
                required: true,
                customemail: true,
            },
            'password': {
                required: true
            }
        },
        submitHandler : function(form) {
            //do something here
            form.submit();
        }
    });
    $('#form-newsletter-footer').validate({
        rules: {
            'email-newsletter-footer': {
                required: true,
                customemail: true,
            }
        },
        submitHandler : function(form) {
            //do something here
            // form.submit();
        }
    });
    $('.form-contact form').validate({
        rules: {
            // simple rule, converted to {required:true}
            password: "required",
            // compound rule
            email: {
                required: true,
                email: true
            }
        },
        submitHandler : function(form) {
            //do something here
            form.submit();
        }

    });
    $('form[name="form-message2"]').validate({
        rules: {
          'msg-object2': {
            required: true,
          },
          'msg-content2': {
            required: true,
            maxlength: 256,
          }
        },
        submitHandler : function(form) {
            //do something here
            //form.submit();
        }

    });
  $('form[name="contact"]').validate({
    rules: {
      // simple rule, converted to {required:true}
      "contact[firstName]": "required",
      "contact[phone]": {
          "customphone" : {       
            depends: function (element) {
              if($("#contact_phone").val()==""){
                return false;
              }
              else
              {
                return true;
              }
            }  
          },
      }, 
      "contact[object]": "required",
      "contact[message]": {
        required: true,
      },
        "contact[email]": {
            required: true,
            customemail: true,
        }
    },
    submitHandler : function(form) {
      if (grecaptcha.getResponse()) {
          form.submit();
      } else {
          grecaptcha.reset();
          grecaptcha.execute();
      }
    }

  });

    // Sticky header
      $(window).scroll(function(){
        var sticky = $('header'),
            scroll = $(window).scrollTop();

        if (scroll >= 200) {

            var stickyNavigation = $('.navigation-bar').parent(),
                navigationBar = $('.navigation-bar');
            if (stickyNavigation.length) {
                navigationBar.parent().prev().prev('header').css('box-shadow', 'none');
            }

            sticky.addClass('sticky');
        } else {
            sticky.removeClass('sticky');
        }
      });

    // Calculate Header height and apply the result for the Body as a paddingTop
    $('header:not(#Login .header)').next().css('marginTop', $('header').innerHeight());



  $('#login').validate({
    rules: {
      // simple rule, converted to {required:true}
      password: "required",
      // compound rule
      email: {
        required: true,
        email: true
      }
    },
    submitHandler : function(form) {
      //do something here
      form.submit();
    }

  });

    $('.forget-password form[name="form"]').validate({
        rules: {
            'form[email]': {
                required: true,
                email: true
            }
        },
        submitHandler : function(form) {
            //do something here
            form.submit();
        }

    });
  
  $('form[name="startuper_registration"]').validate({
    rules: {
      // simple rule, converted to {required:true}
      "startuper_registration[profile]": "required",
      "startuper_registration[firstName]": "required",
      "startuper_registration[password][first]": {
        required: true,
        minlength: 8
      },
      "startuper_registration[password][second]": {
        required: true,
        minlength: 8,
        equalTo: '#startuper_registration_password_first'
      },
      // compound rule
      "startuper_registration[lastName]": {
        // required: true,
        required: true
      },
      "startuper_registration[phone]": {
        // required: true,
        required: false,
        maxlength: 9,
        minlength: 9
      },
      "startuper_registration[address]": {
        // required: true, 
        required: true,
        maxlength: 256
      },
      "startuper_registration[email]": {
        required: true,
        customemail: true
      },
      "startuper_registration[birthday]": {
        required: true,
        date: true
      }
    },
    
    errorPlacement: function(error, element) {
      if (element.attr('type')=='radio') 
      {
        error.insertAfter($(element).parents('.form-check').parent('.col').parent('.row').find('.col').last()); //So i putted it after the .form-group so it will not include to your append/prepend group.
      }
      else
      {
        error.insertAfter(element);
      }
    }, 
    highlight: function(element) {
        $(element).closest('.form-check').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-check').removeClass('has-error');
    },
    invalidHandler: function(form, validator) {
                
        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },
    submitHandler : function(form) {
        form.submit();
        /*if (grecaptcha.getResponse()) {
            form.submit();
        } else {
            //grecaptcha.reset();
            grecaptcha.execute();
        }*/
    }

  });

  $('form[name="message_response"]').validate({  
    rules: {
      "message_response[content]": {
        required: true,
        maxlength: 256
      }
    },
      submitHandler: function submitHandler(form) {
          if (grecaptcha.getResponse()) {
              form.submit();
          } else {
              //grecaptcha.reset();
              grecaptcha.execute();
          }
      }
  });

  $('form[name="message"]').validate({  
    rules: {
      "message[object]": {
        required: true,
        maxlength: 100
      },
      "message[_token]": {
        required: true,
        maxlength: 256
      }
    },
      submitHandler: function submitHandler(form) {
          if (grecaptcha.getResponse()) {
              form.submit();
          } else {
              grecaptcha.reset();
              grecaptcha.execute();
          }
      }
  });

  $('form[name="change_cover_project"]').validate({  
    rules: {
      "change_cover_project[imageFile][file]": {
        required: true,
      }
    }
  });

    $('form[name="change_logo_project"]').validate({
        rules: {
            "change_logo_project[logoFile][file]": {
                required: true,
            }
        }
    });

  $('#form-send-message').validate({  
    rules: {
      "msg-object": {
        required: true,
      }, 
      "msg-content": {
        required: true,
      }
    },
      invalidHandler: function invalidHandler(form, validator) {
          // grecaptcha.reset();
          //grecaptcha.execute();
      },
      submitHandler: function submitHandler(form) {
          // if (grecaptcha.getResponse()) {
              //form.submit();
          // } else {
              //grecaptcha.reset();
              // grecaptcha.execute();
          // }
      }
  });

  $('#form-ask-documentation').validate({
    rules: {
      "document": {
        required: true,
        // {       
        //   depends: function (element) { 
        //     if($("#customCheck237").is(':checked') || $("#customCheck238").is(':checked') || $("#customCheck239").is(':checked') || $("#customCheck240").is(':checked')){
        //       return false;
        //     } else {
        //       return true;
        //     }
        //   }  
        // }
      },
      "message-request": {
        required: false,
        // maxlength: 256
      },
      "accept": {
        required: true,
      }
      // submitHandler : function(form) {
      //   form.submit();
      // }
    },
    errorPlacement: function(error, element) {
      if (element.attr('type')=='checkbox') 
      {
        error.insertAfter($(element).parents('.form-group').find('.custom-control').last()); //So i putted it after the .form-group so it will not include to your append/prepend group.
      }
      else
      {
        error.insertAfter(element);
      }
    }, 
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    invalidHandler: function invalidHandler(form, validator) {
        // grecaptcha.reset();
        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },
    submitHandler : function(form) {
      //do something here
      //form.submit();
    }
  });

  $('#form-offer-request').validate({  
    rules: {
      "f-name": {
        required: true,
      }, 
      "l-name": {
        required: true,
      },
      "email": {
        required: true,
        customemail: true
      },  
      "type": { 
        required: true
      }
    },
      // invalidHandler: function invalidHandler(form, validator) {
      //     grecaptcha.reset();
      //     grecaptcha.execute();
      // },
      // submitHandler: function submitHandler(form) {
      //     if (grecaptcha.getResponse()) { 
      //        // form.submit();
      //     } else {
      //         grecaptcha.reset();
      //         grecaptcha.execute();
      //     }
      // }

  });

  $('form[name="change_password"]').validate({
    rules: {
      // simple rule, converted to {required:true}
      "change_password[currentPassword]": "required", 
      "change_password[newPassword]": {
        required: true,
        minlength: 8,
      },      
      "change_password[confirmPassword]": { 
        required: true,
        minlength: 8,
        equalTo: '#change_password_newPassword'
      },      
    },
    submitHandler : function(form) {
        if (grecaptcha.getResponse()) {
            form.submit();
        } else {
            //grecaptcha.reset();
            grecaptcha.execute();
        }
    }

  });


  // var iframe = $('#cke_1_contents .cke_wysiwyg_frame.cke_reset');
  // // Get the document within the <iframe>
  // var doc = iframe.contentDocument || frame.contentWindow.document;
  // //now using jQuery
  // jQuery(doc.body).find('form#post').validate({
  //   rules: {
  //     // simple rule, converted to {required:true}
  //     "change_password[currentPassword]": "required",
  //     "change_password[newPassword]": {
  //       required: true,
  //       minlength: 8,
  //     },      
  //     "change_password[confirmPassword]": {
  //       required: true,
  //       minlength: 8,
  //       equalTo: '#change_password_newPassword'
  //     },      
  //   },
  //   submitHandler : function(form) {
  //     //do something here
  //     form.submit();
  //   }
  // });


  $('form[name="project_form"]').validate({
    ignore: [],
    rules: {
      // Step 1

          // "project_form[language]": "required",
          // "project_form[language]": {
          //   required: true,
          // },
          "project_form[name]": {
            required: true,
            maxlength: 100
          },
          "project_form[description]": {
            required: true,
            maxlength: 256
          },
          "txt-first-avantage": {
            required: false,
            maxlength: 120
          },
          "txt-first-finance": {
            required: false,
            maxlength: 120
          },
          "project_form[summary]": {
            required: false,
            maxlength: 3000
          },
          "project_form[mainProducts]": {
            required: true,
            maxlength: 256
          },
          "project_form[denomination]": {
              required: {       
                depends: function (element) {
                  if($("#project_form_startup_0").is(':checked')){
                    return true;
                  }
                  else
                  {
                    return false;
                  }
                }  
              },
              maxlength: 100
          }, 
          "project_form[rc]": {
              required: {       
                depends: function (element) {
                  if($("#project_form_startup_0").is(':checked')){
                    return true;
                  }
                  else
                  {
                    return false;
                  }
                }  
              },
              maxlength: 100
          }, 
          "project_form[creatingDate]": {
              required: {       
                depends: function (element) { 
                  if($("#project_form_startup_0").is(':checked')){
                    return true;
                  }
                  else
                  {
                    return false;
                  }
                }  
              }
          },
          "project_form[city]": {
              required: {       
                depends: function (element) { 
                  if($("#project_form_startup_0").is(':checked')){
                    return true;
                  }
                  else
                  {
                    return false;
                  }
                }  
              }
          }, 

      // End Step 1

      // Step 2

      "project_form[avantages]": "required",
      // "project_form[amount]": "required",
      "project_form[projectFinances]": "required",
      // "project_form[amount]": "required",
      "project_form[morocco]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherCountry").is(':checked')){
                return false;
              }
              else
              {
                return true;
              }
            }  
          }
      },
      "project_form[raised]": {
        required: false
      },
      "project_form[express]": {
        required: false,
        maxlength: 10000
      },
      // "project_form[budget]": {
      //   required: false
      // },
      "project_form[salesChannels][]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherSalesChannels").is(':checked')){
                return false;
              }
              else
              {
                return true;
              }
            }  
          }
      },
      "project_form[sectors][]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherSectors").is(':checked')){
                return false;
              }
              else
              {
                return true;
              }
            }  
          }
      },
      "project_form[businessModels][]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherBusinessModel").is(':checked')){
                return false;
              }
              else
              {
                return true;
              }
            }  
          }
      },
      "project_form[moreSalesChannels]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherSalesChannels").is(':checked')){
                return true;
              }
              else
              {
                return false;
              }
            }  
          }
      }, 
      "project_form[foreignCountry]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherCountry").is(':checked')){
                return true;
              }
              else
              {
                return false;
              }
            }  
          }
      }, 
      "project_form[moreBusinessModel]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherBusinessModel").is(':checked')){
                return true;
              }
              else
              {
                return false;
              }
            }  
          }
      }, 
      "project_form[moreSectors]": {
          required: {       
            depends: function (element) { 
              if($("#project_form_otherSectors").is(':checked')){
                return true;
              }
              else
              {
                return false;
              }
            }  
          }
      }, 
      "project_form[email]": {
        required: true,
        email: true
      },
      
      // End Step 2
      
      // Step 3

      // "project_form[teamMembers][][firstName]": "required",
      // "project_form[teamMembers][][lastName]": "required",
      // "project_form[teamMembers][][position]": "required",

      
      "project_form[teamMembers][0][firstName]": {
        required: true,
        maxlength: 50
      },
      "project_form[teamMembers][0][lastName]": {
        required: true,
        maxlength: 50
      },
      
      "project_form[teamMembers][0][biography]": {
        required: false,
        maxlength: 400
      },

      // End Step 3 
    },
     errorPlacement: function(error, element) {
      if (element.attr('type')=='radio' || element.attr('type')=='checkbox') 
      {
        if (element.attr('type')=='checkbox') {
          error.insertAfter($(element).parents('.parnet-check-fieldset').find('.error-check-placement')); //So i putted it after the .form-group so it will not include to your append/prepend group.
        }else{
          error.insertAfter($(element).parents('.form-group')); //So i putted it after the .form-group so it will not include to your append/prepend group.
        }
      }
      else
      {
        error.insertAfter(element);
      }
    }, 
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    invalidHandler: function(form, validator) {
                
        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },


    submitHandler : function(form) {
        if (grecaptcha.getResponse()) {
            form.submit();
        } else {
            grecaptcha.reset();
            grecaptcha.execute();
        }
    }
  });


$("form[name='company']").validate({
    rules: {
        "company[name]": {
            required: true
        },
        "company[description]": {
            required: true,
            maxlength: 10000
        },
        "company[duration]": {
            required: true
        },
        "company[fundingObjective]": {
            required: true
        },
        "company[RIB]": {
            required: true
        },
        "company[isLegalRepresentativeOfTheAssociation]": {
            required: true
        },
        "company[isAcceptedTheConditionOfSecurity]": {
            required: true
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr('type')=='radio' || element.attr('type')=='checkbox') {
            error.insertAfter($(element).parents('.form-check'));
        } else if (element.attr('id')=='company_duration') {
            error.insertAfter($(element).parents('.custom-select-field'));
        } else {
            error.insertAfter(element);
        }
    },
    invalidHandler: function(form, validator) {

        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },
    submitHandler: function(form) {
        form.submit();
    }
});

$("form[name='contributor']").validate({
    rules: {
        "contributor[firstName]": {
            required: true
        },
        "contributor[lastName]": {
            required: true
        },
        "contributor[email]": {
            required: true,
            customemail: true
        },
        "contributor[contributionAmount]": {
            required: true
        },
        "contributor[chosenPayment]": {
            required: true
        },
        "contributor[amountDebited]": {
            required: true
        },
        "cgv": {
            required: true
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr('type')=='radio' || element.attr('type')=='checkbox') {
            error.insertAfter($(element).parents('#contributor_chosenPayment'));
            error.insertAfter($(element).parents('.controls'));
        } else {
            error.insertAfter(element);
        }
    },
    invalidHandler: function(form, validator) {

        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },
    submitHandler: function(form) {
        form.submit();
    }
});

$("form[name='company_comment']").validate({
    rules: {
        "company_comment[content]": {
            required: true
        }
    },
    errorPlacement: function(error, element) {
        if (element.attr('type')=='radio' || element.attr('type')=='checkbox') {
            error.insertAfter($(element).parents('.form-check'));
        } else {
            error.insertAfter(element);
        }
    },
    invalidHandler: function(form, validator) {

        if (!validator.numberOfInvalids())
            return;
        $(validator.errorList[0].element).focus();
        console.log(validator.errorList[0].element);
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-150
        }, 1000);
    },
    submitHandler: function(form) {
        form.submit();
    }
});

  // hide if other is unchecked

    if($("#project_form_otherBusinessModel").is(':checked')){
      $('#project_form_moreBusinessModel').show();
    }
    if($("#project_form_otherSectors").is(':checked')){
      $('#project_form_moreSectors').show();
    }
    if($("#project_form_otherSalesChannels").is(':checked')){
      $('#project_form_moreSalesChannels').show();
    }
    if($("#project_form_otherCountry").is(':checked')){
      $('#project_form_foreignCountry').show();
    }





  // stigky navigationBar onScroll for page project
  if ($(window).width() > 1023) {
      if ($('.navigation-bar').length) {
        $(window).scroll(function() {
            $(".navigation-bar").sticky({topSpacing: 0});
        })
      }
  }

  if ($(window).width() >= 768) {
    if ($('.b-create-project').length) {
      $(window).scroll(function(){
        var stickyElement = $('.block-subbmission').outerHeight();
        var windowHeight = $(window).outerHeight();
        var target = $('.b-footer').offset().top - windowHeight - stickyElement;
        
        if($(window).scrollTop() <= target){
          // $('.block-subbmission-placement').css('height', stickyElement);
          $('.block-subbmission').css({
            'position': 'fixed',
            'bottom': '0',
            'display': 'block'
          });
        } else {
          // $('.block-subbmission-placement').css('height', 0);
          $('.block-subbmission').css({
            'position': 'relative',
            'bottom': '-100%',
            'display': 'none'
          });
        }
      });
    }
  }

    // Scroll To The Search Result Elements
    var searchResult = jQuery('.container.result-listing');
    if (searchResult.length) {
        jQuery('html, body').animate({
            scrollTop: searchResult.offset().top - jQuery('header').innerHeight()
        }, 1000);
    }

    // Clone Technical Sheet For Fiche Project Page
    if ($(window).width() < 768) {
        if ($('.b-project-content').length) {
            $('.b-project-content .info-card:not(.detailled)').clone().prependTo(".b-project-content .content-bar");
        }
    }

  // Make Preject Gallery Slide on Mobile  (Small Devices)
  var projectGallery = $('.project-gallery');
  if (projectGallery.length) {
    $('.project-gallery-content__show').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      dots: false,
      fade: true,
      asNavFor: '.project-gallery-content__select'
    });
    $('.project-gallery-content__select').slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      asNavFor: '.project-gallery-content__show',
      dots: false,
      arrows: false,
      centerMode: true,
      focusOnSelect: true,
      responsive: [
        {
          breakpoint: 767,
          settings: {
            slidesToShow: 3,
            infinite: true
          }
        }
      ]
    });
    if ($('.project-gallery-content__select .project-gallery-img').length < 2) {
      $('.project-gallery-content__select').hide();
    }
  }

  // input number style
  $("form[name='project_form'] input[type='number']").inputSpinner({
    buttonsClass: "btn-bg-blue",
  });

    // CUSTOM SLIDER FOR DISCOBER BEST PROJECT BLOCK HP
    if ( $( ".slider-categories" ).length ) {

      var $slickCategories = $('.slider-categories');
      $slickCategories.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 1500,
        swipeToSlide: false,
        swipe: false,
        speed: 800,
        dots: false,
        arrows: false,
        vertical: true,
        verticalSwiping: true,
        pauseOnHover: false,
        pauseOnFocus: false,
        // variableWidth: true,
        // responsive: [
        //   {
        //     breakpoint: 767,
        //     settings: {
        //       slidesToShow: 3,
        //       infinite: true
        //     }
        //   }
        // ]

      });

      // Show Only First 5 Elements From Categories On Mobile
        if ($('.b-discover').length) {
            if ($(window).width() < 642) {
                $('div.categories-tags .btn:lt(5)').css("display", "inline-block");
            }
        }

      if ($(window).width() > 1024) {
        $slickCategories.on('afterChange', function(event, slick, currentSlide, nextSlide){
          var $current = $(this).find('.slick-current');
          var w = $current.outerWidth(false);
          $current.parent().parent().parent().css({
            width: w
          });
       });
      } 
      // else if ($(window).width() < 840) {
      //   $slickCategories.on('afterChange', function(event, slick, currentSlide, nextSlide){
      //     var $currentP = $(this).find('.slick-current p');
      //     var $currentSlider = $(this).find('.slick-current');
      //     console.log($currentP);
      //     var h = $currentP.outerHeight(false); 
      //     console.log(h);
      //     $currentSlider.parent().parent().parent().css({
      //       height: h
      //     });
      //  });
      // }
      // $('.slider-categories .slick-active p').each(function(index) {
      //     var elem  = $(this).innerWidth();
      //     $(this).paret().parent('slick-slide').css("width", elem);
      // });
    };
    // if ( $( ".slider-categories" ).length ) {
    //   $('.slider-categories p').each(function(index) {
    //       var elem  = $(this).width();
    //       $( ".slider-categories" ).css("width", elem)
    //   });

    //   $('.slider-categories').jCarousel({
    //       type:'slidey-up',
    //       circle: {
    //           isshow:false,
    //       },
    //       arrow: {
    //           isshow:false,
    //       },
    //       auto: {
    //           isauto:true,
    //           interval:2500
    //       },
    //       carsize: {
    //           carheight:60
    //       },
    //   });
    // };

    
    $(".detail-item .share").click(function() { $(this).next().toggleClass('show') });

if ($('form[name="startuper_registration"]').length) {
  $('input[name="startuper_registration[phone]"]').mask('000000000');
}

if ($('form[name="contact"]').length) {
  $('input[name="contact[phone]"]').mask('000000000');
}

if ($('form[name="project_form"]').length) {
  $('input[name="project_form[rc]"]').mask('000000');
}

if ($('form[name="company"]').length) {
    $('[name="company[fundingObjective]"]').keypress(function (e) {
        if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
    });
}

if ($('#accordion').length) {
    if ($(window).width() < 993) {
      $('#accordion .card-header button').attr("aria-expanded","false");
      $('#accordion .card-header').next('.collapse').removeClass('show');
    }
}

$('[data-toggle="tooltip"]').tooltip();



if ($(window).width() < 1025) {
  // Hide Header on on scroll down
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $('.main-header').outerHeight();
    var activateSticky = $('.main-header').outerHeight() + 100;


    $(window).scroll(function(event){
        didScroll = true;
    });

    setInterval(function() {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);
    function hasScrolled() {
        var st = $(window).scrollTop();
        // scrolled passed the height of header
        setTimeout(function () {
            if (st > activateSticky) {
                $('.main-header').addClass("sticky-active");
            } else {
                $('.main-header').removeClass("sticky-active");
            }
        },300);

        // Make scroll more than delta
        if(Math.abs(lastScrollTop - st) <= delta)
            return;

        // If scrolled down and past the navbar, add class .nav-up.
        if (st > lastScrollTop && st > navbarHeight){
            // Scroll Down
            $('.main-header').removeClass('nav-down').addClass('nav-up');
        } else {
            // Scroll Up
            if(st + $(window).height() < $(document).height()) {
                $('.main-header').removeClass('nav-up').addClass('nav-down');
            }
        }

        lastScrollTop = st;
    }
  }
});


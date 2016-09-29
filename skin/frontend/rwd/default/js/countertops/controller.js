var project = angular.module('countertops', ['ngSanitize']);

project.controller('appCtrl', appCtrl);

function appCtrl($scope, $timeout, $http) {

    // filters are slow, so we cache them into divs

  $scope.allFilters = function(thickness,price,color,type,veins,tones,remenants,filter){
            
            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1); 
            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/filters';

            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: thickness,
                   price:price,
                   color:color,
                   type:type,
                   veins:veins,
                   tones:tones,
                   remenants:remenants,
                   slabwidth:slabwidth,
                   slabdepth:slabdepth
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {

                if (filter == 'filter'){
                    jQuery("#filters").html(data);
                }else{
                    if (thickness == 2){
                        jQuery(".filters2").html(data);
                    }
                    if (thickness == 3){
                        jQuery(".filters3").html(data);
                    }   
                }
                return;             
            });
    }

    $scope.allFilters(2,'all','all','all','all','all','');
    $scope.allFilters(3,'all','all','all','all','all','');

    // reload variables
    var rwidth = jQuery('.shrink-width-span').html();
    rwidth = rwidth.substring(0, rwidth.length-1);
    if (rwidth == ''){
        rwidth = 61;
        jQuery('.shrink-width-span').html(rwidth+'"');
    }
    var rheight = jQuery('.shrink-height-span').html();
    rheight = rheight.substring(0, rheight.length-1);
    if (rheight == ''){
        rheight = 22;
        jQuery('.shrink-height-span').html(rheight+'"');
    }
    var thickness = jQuery('.rthickness').html();
    if (thickness == ''){
        thickness = 3;
    }
    var setwidth = rwidth*8;
    jQuery('.shrink-main-width').width(setwidth);
    jQuery('.cp-counter').width(setwidth);
    jQuery('.cp-counter-front').width(setwidth);
    jQuery('.cp-text-front').width(setwidth);
    var edgename = jQuery('.edgename').html();
    jQuery('.cp-counter-front .edging').html(edgename);
    jQuery('.cp-counter-back .edging').html(edgename);
    jQuery('.cp-counter-left .edging').html(edgename);
    jQuery('.cp-counter-right .edging').html(edgename);

    $scope.mywidth = rwidth;
    $scope.myheight = rheight;
    $scope.mythickness = { size: thickness };
    $scope.showHoles = false;
    $scope.showVessels = false;
    $scope.showVessels2 = false; 
    $scope.faucet1 = 10;
    $scope.faucet2 = 10;
    $scope.ready = 0;

    //********************************************************************************
    // Restore Layout
    //********************************************************************************
    var skunum = jQuery('.skunum').html();


    if (skunum != ''){
        var sinknum = jQuery('.sinknum').html();
         var sinksku = jQuery('.sinksku').html();
        var sinksize = jQuery('.sinksize').html();
        var sinktype = jQuery('.sinktype').html();
        var sink1dis = jQuery('.sink1dis').html();
        var sink2dis = jQuery('.sink2dis').html();
        var faucettype = jQuery('.faucettype').html();

        var v1left = jQuery('.vessel1left').html();
        var v1top = jQuery('.vessel1top').html();
        var v1size = jQuery('.vesselsize1').html();
        var v2left = jQuery('.vessel2left').html();
        var v2top = jQuery('.vessel2top').html();
        var v2size = jQuery('.vesselsize2').html();
        var faucet1degree = jQuery('.faucet1degree').html();
        var faucet2degree = jQuery('.faucet2degree').html();
        var faucet1dis = jQuery('.faucet1dis').html();
        var faucet2dis = jQuery('.faucet2dis').html();
        var polishleft = jQuery('.polishleft').html();
        var polishback = jQuery('.polishback').html();
        var polishright = jQuery('.polishright').html();
        var backsplashleft = jQuery('.backsplashleft').html();
        var backsplashback = jQuery('.backsplashback').html();
        var backsplashright = jQuery('.backsplashright').html();

        // only do this if we have a sink
        if (sinksku != ''){
            sink1dis = parseFloat(sink1dis);
            sink2dis = parseFloat(sink2dis);
        
            sinktype = sinktype.toLowerCase();
            sinktype = sinktype.replace(" ", "-");
            jQuery('.sink1-display').addClass(sinktype);
            var sinkshapesize = jQuery('.'+sinktype).width();
            var halfshapesive = sinkshapesize/2;
            var centerhole = halfshapesive-7;

            var halfsink = roundme(sinksize)/2;
            var halfsinkpx = halfsink*8;
            sink1left = (sink1dis-halfsink)*8;
            sink2left = (sink2dis-halfsink)*8;
            var sink1line = sink1dis*8;
            var sink2line = sink2dis*8;

            var squarecheck = sinktype.search("square");

            jQuery('.sink1-display').css('display','block');
        }
        
        var edging = jQuery('.edgetype').html();
        if (polishleft == 1){
            
            jQuery('.cp-counter-left').css({"display":"block","border-bottom":"1px solid #000","border-left":"1px solid #000", "border-top":"1px solid #000"});
            jQuery('.cp-counter-left .polish').css('display', 'block');
            jQuery('.cp-counter-left .edging').css('display', 'block');
            jQuery('.cp-counter-left .edging').css('margin-top', '90px');
            jQuery('.cp-counter-left .edging').html(edging);
        }

        if (polishback == 1){
  
            jQuery('.cp-counter-back').css({"display":"block","border-left":"1px solid #000","border-top":"1px solid #000", "border-right":"1px solid #000"});
            jQuery('.cp-counter-back .polish').css('display', 'inline');
            jQuery('.cp-counter-back .edging').css('display', 'inline');
            jQuery('.cp-counter-back .edging').html(edging);
        }        

        if (polishright == 1){
  
            jQuery('.cp-counter-right').css({"display":"block","border-top":"1px solid #000","border-right":"1px solid #000", "border-bottom":"1px solid #000"});
            jQuery('.cp-counter-right .polish').css('display', 'block');
            jQuery('.cp-counter-right .edging').css('display', 'block');
            jQuery('.cp-counter-right .edging').html(edging);
        } 

        if (backsplashleft == 1){
            jQuery('.cp-counter-left').css({"display":"block","background-color":"#f1ffbc"});
            jQuery('.cp-counter-left .backsplash').css('display', 'block');
        }

        if (backsplashback == 1){
            jQuery('.cp-counter-back').css({"display":"block","background-color":"#f1ffbc"});
            jQuery('.cp-counter-back').css({"border-left":"0px","border-top":"0px", "border-right":"0px"});
            jQuery('.cp-counter-back .backsplash').css('display', 'block');            
        }

        if (backsplashright == 1){
            jQuery('.cp-counter-right').css({"display":"block","background-color":"#f1ffbc"});
            jQuery('.cp-counter-right').css({"border-bottom":"0px","border-top":"0px", "border-right":"0px"});
            jQuery('.cp-counter-right .backsplash').css('display', 'block');            
        }


        if (sinktype != 'vessel' && sinktype != ''){

            jQuery('.sink1-display').css('left',sink1left+'px');
            jQuery('.sink1-display-dash-line').css('display','block');
            jQuery('.sink1-distance').html(sink1dis+'"');
            jQuery('.sink1-display-dash-line').css({"display": "block", "width": sink1line+"px"});
            jQuery('.sink1-distance').css({"display": "block", "left": (sink1line+2)+"px"}); 

            if (squarecheck > 0){
                var htp = "-22px";
            }else{
                var htp = "-18px";
            }

            if (faucettype == 'holes-three'){
                
                jQuery('.sink1-lefthole').css({"display": "block", "left": (centerhole-28)+"px", "top": htp});
                jQuery('.sink1-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                jQuery('.sink1-righthole').css({"display": "block", "left": (centerhole+28)+"px", "top": htp});

                if (sinknum == 2){
                    jQuery('.sink2-lefthole').css({"display": "block", "left": (centerhole-28)+"px", "top": htp});
                    jQuery('.sink2-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                    jQuery('.sink2-righthole').css({"display": "block", "left": (centerhole+28)+"px", "top": htp});
                }
            }

            if (faucettype == 'holes-two'){
                
                jQuery('.sink1-lefthole').css({"display": "block", "left": (centerhole-18)+"px", "top": htp});
                jQuery('.sink1-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                jQuery('.sink1-righthole').css({"display": "block", "left": (centerhole+18)+"px", "top":htp});

                if (sinknum == 2){
                    jQuery('.sink2-lefthole').css({"display": "block", "left": (centerhole-18)+"px", "top": htp});
                    jQuery('.sink2-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                    jQuery('.sink2-righthole').css({"display": "block", "left": (centerhole+18)+"px", "top": htp});
                }
            }

            if (faucettype == 'holes-one'){
                
                
                jQuery('.sink1-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                

                if (sinknum == 2){
                
                    jQuery('.sink2-middlehole').css({"display": "block", "left": centerhole+"px", "top": "-22px"});
                
                }
            }

        }else if (sinktype != ''){
            
            var halfhole = v1size/2;
            var sink1left = (v1left-halfhole)*8;
            var sink1top = v1top*8;
            var sink1size = v1size*8;

            jQuery('.sink1-display').css('left',sink1left+'px');
            jQuery('.sink1-display').css('top',sink1top+'px');
            jQuery('.sink1-display').css('width',sink1size+'px');
            jQuery('.sink1-display').css('height',sink1size+'px');
            jQuery('.sink1-display').css('border-radius',(sink1size/2)+'px');
            jQuery('.sink1-display-dash-line').css('display','block');
            jQuery('.sink1-distance').html(v1left+'"');
            sink1line=v1left*8;
            jQuery('.sink1-display-dash-line').css({"display": "block", "width": sink1line+"px"});
            jQuery('.sink1-display-dash-line').css("top", sink1top+sink1size+"px");
            jQuery('.sink1-distance').css({"display": "block", "left": (sink1line+2)+"px"});
            jQuery('.sink1-distance').css("top", sink1top+sink1size-5+"px"); 

            if (v1size == 1.75){
                jQuery('.centerhole1').css('border','0px');
            }

            if (faucet1degree == 1){
                faucet1dis = faucet1dis*8;
                var mfdis = faucet1dis-(faucet1dis*2);
                var mfdisp = mfdis-60;
                jQuery('.centerhole1').css('display','block');
                jQuery('.left45').css('display','block');
                jQuery('.left45').css('top',mfdisp+'px');
                jQuery('.left45').css('left',mfdis+'px');
                jQuery('.left45 .line1').css('width',faucet1dis+'px');
                jQuery('.left45 .line1').css('height',(faucet1dis+60)+'px');
                
            }

            if (faucet1degree == 2){
                var f1dis = faucet1dis*8;
                var f1dleft = (sink1size/2)-2;
                jQuery('.centerhole1').css('display','block');
                jQuery('.center1').css('display','block');
                jQuery('.center1').css('height',f1dis+'px');
                jQuery('.center1').css('top','-'+f1dis+'px');
                jQuery('.center1').css('left','4px');
            }

            if (faucet1degree == 3){
                faucet1dis = faucet1dis*8;
                var mfdis = faucet1dis+(faucet1dis*2);
                var mfdisp = mfdis+60;
                jQuery('.centerhole1').css('display','block');
                jQuery('.right45').css('display','block');
                jQuery('.right45').css('left','8px');
                jQuery('.right45 .sinkholeright1').css('top',(faucet1dis-2)+'px');
                jQuery('.right45 .sinkholeright1').css('left',(faucet1dis-8)+'px');
                jQuery('.right45 .line1').css('width',(faucet1dis-6)+'px');                                
            }

        }

        if (sinknum == 2 && sinktype != ''){
            jQuery('.sink2-display').addClass(sinktype);
            jQuery('.sink2-display').css('display','block'); 


            if (sinktype != 'vessel'){
                jQuery('.sink2-display').css('left',sink2left+'px');
                jQuery('.sink2-display-dash-line').css({"display": "block", "width": sink2line+"px"});
                jQuery('.sink2-distance').css({"display": "block", "left": (sink2line+2)+"px"}); 
                jQuery('.sink2-distance').html(sink2dis+'"');              
            }else{
                var halfhole2 = v2size/2;
                var sink2left = (v2left-halfhole2)*8;
                var sink2top = v2top*8;
                var sink2size = v2size*8;
                var v2width = v2left*8;

                jQuery('.sink2-display').css('left',sink2left+'px');
                jQuery('.sink2-display').css('top',sink2top+'px');
                jQuery('.sink2-display').css('width',sink2size+'px');
                jQuery('.sink2-display').css('height',sink2size+'px');
                jQuery('.sink2-display').css('border-radius',(sink2size/2)+'px');
                jQuery('.sink2-display-dash-line').css('display','block');
                jQuery('.sink2-distance').html(v2left+'"');
                jQuery('.sink2-display-dash-line').css({"display": "block", "width": v2width+"px"});
                jQuery('.sink2-display-dash-line').css("top", sink2top+sink2size+"px");
                jQuery('.sink2-distance').css({"display": "block", "left": (v2width+2)+"px"});
                jQuery('.sink2-distance').css("top", sink2top+sink2size-5+"px"); 

                if (v2size == 1.75){
                    jQuery('.centerhole2').css('border','0px');
                }

                if (faucet2degree == 1){
                faucet2dis = faucet2dis*8;
                var mfdis2 = faucet2dis-(faucet2dis*2)
                var mfdisp2 = mfdis2-60;
                jQuery('.centerhole2').css('display','block');
                jQuery('.left452').css('display','block');
                jQuery('.left452').css('top',mfdisp2+'px');
                jQuery('.left452').css('left',mfdis2+'px');
                jQuery('.left452 .line1').css('width',faucet2dis+'px');
                jQuery('.left452 .line1').css('height',(faucet2dis+60)+'px');
                }

                if (faucet2degree == 2){
                    var f2dis = faucet2dis*8;                    
                    var f2dleft = (sink2size/2)-2;
                    jQuery('.centerhole2').css('display','block');
                    jQuery('.center2').css('display','block');
                    jQuery('.center2').css('height',f2dis+'px');
                    jQuery('.center2').css('top','-'+f2dis+'px');
                    jQuery('.center2').css('left','4px');
                }

            if (faucet2degree == 3){
  
                faucet2dis = faucet2dis*8;
                var mfdis2 = faucet2dis+(faucet2dis*2);
                var mfdisp2 = mfdis2+60;
                var rightline2 = jQuery('.right452 .line1').width();
                var mftop = (faucet2dis-6)+16;
                //60 = 7.5 = 36
                //36 = 4.5 = 60
                jQuery('.centerhole2').css('display','block');
                jQuery('.right452').css('display','block');
                jQuery('.right452').css('left','8px');
                jQuery('.right452 .sinkholeright2').css('left',(faucet2dis-8)+'px');
                jQuery('.right452 .line1').css('width',(faucet2dis-6)+'px');                                
            }

            }
        }
    }

     $scope.thickness = function(thickness) {
        $scope.mythickness.size = thickness;        
     }

    //********************************************************************************
    // Manage Dimensions Width
    //********************************************************************************
   $scope.widthChanged = function() {

        // hide sinks as we need to re-calculate
         jQuery('.sink1-display').css('display','none');
         jQuery('.sink1-display-dash-line').css('display','none');
         jQuery('.sink1-distance').css('display','none');
         jQuery('.sink2-display').css('display','none');
         jQuery('.sink2-display-dash-line').css('display','none');
         jQuery('.sink2-distance').css('display','none');

         // clear everything
          jQuery('.sinknum').html('');
          jQuery('.sinksku').html('');
          jQuery('.sinktype').html('');
          jQuery('.sinksize').html('');
          jQuery('.sink1dis').html('');
          jQuery('.sink2dis').html('');
          jQuery('.edgetype').html('LAB-ED-EAS');
          jQuery('.edgename').html('Eased Edge');
          jQuery('.edgechoice').html('3');
          jQuery('.skunum').html('');
          jQuery('.polishback').html('');
          jQuery('.polishleft').html('');
          jQuery('.polishright').html('');
          jQuery('.backsplashback').html('');
          jQuery('.backsplashleft').html('');
          jQuery('.backsplashright').html('');
          jQuery('.vesselsize1').html('');
          jQuery('.vesselsize2').html('');
          jQuery('.vessel1left').html('');
          jQuery('.vessel1top').html('');
          jQuery('.vessel2left').html('');
          jQuery('.vessel2top').html('');
          jQuery('.faucettype').html('');
          jQuery('.faucet1degree').html('');
          jQuery('.faucet2degree').html('');
          jQuery('.faucet1dis').html('');
          jQuery('.faucet2dis').html('');
          jQuery(".design-stone").html('');
          jQuery('.design-sinks').html('');
          jQuery('.design-faucets').html('');
          jQuery('.rthickness').html('');

        $scope.mywidth = jQuery('input[name=slabwidth]').val();

        // disable 2cm
        if ($scope.mywidth > 98){
            jQuery('input[name=thickness][type="radio"]:first').attr('disabled', true);
            jQuery('input[name=thickness][type="radio"]:eq(1)').click();
        }else{
            jQuery('input[name=thickness][type="radio"]:first').attr('disabled', false);
        }

        if ($scope.mywidth > 100){
            jQuery('.countertop-slab').css('width', '100%');
        }else{
            jQuery('.countertop-slab').css('width', '74%');
        }
        var number = roundme($scope.mywidth);

        var total = number * 8;  
        if (total >= 184 && total <= 960){
            jQuery('.cp-counter').css('width', total + "px");
            jQuery('.cp-counter-back').css('width', total + "px");
            jQuery('.cp-counter-front').css('width', total + "px");
            jQuery('.cp-text-front').css('width', total + "px");
            jQuery('.shrink-main-width').css('width', total + "px");
            jQuery('.shrink-main-width .shrink-width-span').html(number+'"');
        }
    }

    //********************************************************************************
    // Manage Dimensions Height
    //
    // this is not an acurate one to one ratio. To adjust the height you need to adjust
    // the IF statement checking the total and low or raise whichever size you need to 
    // adjust. Then change the sizing and look the slab to see what height it is at
    // when you change to that size. Then you can adjust the limits of the total.    
    //********************************************************************************
   $scope.heightChanged = function() {

        // hide sinks as we need to re-calculate
         jQuery('.sink1-display').css('display','none');
         jQuery('.sink1-display-dash-line').css('display','none');
         jQuery('.sink1-distance').css('display','none');
         jQuery('.sink2-display').css('display','none');
         jQuery('.sink2-display-dash-line').css('display','none');
         jQuery('.sink2-distance').css('display','none');

         // clear everything
          jQuery('.sinknum').html('');
          jQuery('.sinksku').html('');
          jQuery('.sinktype').html('');
          jQuery('.sinksize').html('');
          jQuery('.sink1dis').html('');
          jQuery('.sink2dis').html('');
          jQuery('.edgetype').html('LAB-ED-EAS');
          jQuery('.edgename').html('Eased Edge');
          jQuery('.edgechoice').html('3');
          jQuery('.skunum').html('');
          jQuery('.polishback').html('');
          jQuery('.polishleft').html('');
          jQuery('.polishright').html('');
          jQuery('.backsplashback').html('');
          jQuery('.backsplashleft').html('');
          jQuery('.backsplashright').html('');
          jQuery('.vesselsize1').html('');
          jQuery('.vesselsize2').html('');
          jQuery('.vessel1left').html('');
          jQuery('.vessel1top').html('');
          jQuery('.vessel2left').html('');
          jQuery('.vessel2top').html('');
          jQuery('.faucettype').html('');
          jQuery('.faucet1degree').html('');
          jQuery('.faucet2degree').html('');
          jQuery('.faucet1dis').html('');
          jQuery('.faucet2dis').html('');
          jQuery(".design-stone").html('');
          jQuery('.design-sinks').html('');
          jQuery('.design-faucets').html('');
          jQuery('.rthickness').html('');

        $scope.myheight = jQuery('input[name=slabheight]').val();
        var number = roundme($scope.myheight);

        // Change the height of the box
        if (number > 14.75 && number < 27.25){
            var boxchange = ((number-22)*8)+300;
        }
         jQuery('.countertop-slab').css('height', boxchange);

        //align left side polish area to size of box
        var addme = ((number*8)-120)+34;
        jQuery('.cp-counter-left .edging').css('margin-top',addme);

        var total = number * 8;
        if (total >= 120 && total <= 208){
            jQuery('.cp-counter').css('height', total + "px");
            jQuery('.cp-counter-left').css('height', total + "px");
            jQuery('.cp-counter-right').css('height', total + "px");
            jQuery('.shrink-main-height').css('height', total + "px");
            jQuery('.shrink-main-height span').html(number+'"');
            
            //disable 2cm thickness from 23-25
            if (number >= 23 && number <= 26){
                 jQuery('input[name=thickness][type="radio"]:first').attr('disabled', true);
                 jQuery('input[name=thickness]:eq(1)').click();
            }else{
                jQuery('input[name=thickness][type="radio"]:first').attr('disabled', false);
            }            
        }
    }

   $scope.$watch("mythickness.size", function(newValue, oldValue) {
        jQuery('.design-thickness').html('<span>THICKNESS:</span> ' + $scope.mythickness.size + 'cm');
        
        var reload = jQuery('.rthickness').html();
        if (reload == ''){
            jQuery('.design-stone').html('');
            jQuery('.skunum').html('');
        }
    });

     //handle method for width blur
    $scope.doneWidth = function () {
        var number = roundme($scope.mywidth);
        if (number < 23){number = 23;}
        if (number > 120){number = 120;}
        jQuery('input[name=slabwidth]').val(number);
        $scope.widthChanged();
    }

    //handle method for height blur
    $scope.doneHeight = function () {
        var number = roundme($scope.myheight);
        if (number < 15){number = 15;}
        if (number > 26){number = 26;}
        jQuery('input[name=slabheight]').val(number);

        $scope.heightChanged();   
    }

    // sink inputs

   $scope.sinkChanged = function(sink) {

        if (sink == 1){
            $scope.sink = jQuery('.sink1-distance').html();
            $scope.sinkval = jQuery('.bowl1-distance').val();
            
            moveSink1(sink);
        }

        if (sink == 2){
            $scope.sink = jQuery('.sink2-distance').html();
            $scope.sinkval = jQuery('.bowl2-distance').val();
            moveSink1(sink);
        }        
    
    }

    $scope.doneSink = function (sink) {
        if (sink == 1){
            var totalinches = jQuery('.sink1-distance').html();
            totalinches = totalinches.substring(0, totalinches.length - 1); 
            //totalinches =  Number(totalinches[0]);
            jQuery('.bowl1-distance').val(totalinches); 
        }

         if (sink == 2){
            var totalinches = jQuery('.sink2-distance').html();
            totalinches = totalinches.substring(0, totalinches.length - 1); 
            //totalinches =  Number(totalinches[0]);
            jQuery('.bowl2-distance').val(totalinches); 
        }       
    }

   $scope.vesselChanged = function(vessel) {
        
        if (vessel == 13){
            changeSize(vessel,1);
        }

        if (vessel == 23){
            changeSize(vessel,2);
        }

        if (vessel < 20){
            //$scope.sink = jQuery('.sink1-distance').html();
            //$scope.sinkval = jQuery('.bowl1-distance').val();
            moveSink2(vessel);
        }

        if (vessel > 20){
            //$scope.sink = jQuery('.sink2-distance').html();
            //$scope.sinkval = jQuery('.bowl2-distance').val();
            moveSink2(vessel);
        }        
    
    }

    $scope.doneVessel = function (vessel) {
        if (vessel == 1){
            var totalinches = jQuery('.sink1-distance').html();
            totalinches = totalinches.substring(0, totalinches.length - 1); 
           // totalinches =  Number(totalinches[0]);
            jQuery('.bowl1-distance').val(totalinches); 
        }

         if (vessel == 2){
            var totalinches = jQuery('.sink2-distance').html();
            totalinches = totalinches.substring(0, totalinches.length - 1); 
           // totalinches =  Number(totalinches[0]);
            jQuery('.bowl2-distance').val(totalinches); 
        }       
    }



    // tab handling
     $scope.sections = [
        {name: 'DIMENSIONS', active:true},
        {name: 'STONE'},
        {name: 'EDGING'},
        {name: 'SINKS'},
        {name: 'FAUCETS'},
        {name: 'PRICE'}];
    

    $scope.ctab = {name: 'DIMENSIONS'};
    $scope.start = 1;

    $scope.setBackCheck = function(side){
        var baseurl =  jQuery(".baseurl").html();
        var ajaxurl = baseurl+'countertops/order/checksplash';        
        var slabsku =  jQuery(".skunum").html();
        var backsplashback =  jQuery(".backsplashback").html();
        var backsplashleft =  jQuery(".backsplashleft").html();
        var backsplashright =  jQuery(".backsplashright").html();
        var slabwidth =  jQuery(".shrink-width-span").html();
        slabwidth = slabwidth.substring(0, slabwidth.length - 1);
        var slabheight =  jQuery(".shrink-height-span").html();
        slabheight = slabheight.substring(0, slabheight.length - 1);

       var request = $http({
            method: "post",
            dataType: 'json',
            url: ajaxurl,
            data: {
                slabsku: slabsku,
                slabwidth:slabwidth,
                slabheight:slabheight,
                backsplashback:backsplashback,
                backsplashleft:backsplashleft,
                backsplashright:backsplashright,
                side:side               
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });


        /* Check whether the HTTP Request is successful or not. */
        request.success(function (data) {
            //jQuery(".faucets").html(data);
            var check = Number(data);
            if (data == 1){
                jQuery('.cp-message p').prepend('<span class="cpalert">Your backsplash will come in multiple pieces to meet the width specified.</span>')
            }else if (data != ''){
                var check = data.split(",");
                if (check[1] == 1){
                    chooseBacksplash('left');
                    jQuery('.cp-message p').prepend('<span class="cpalert">There is not enough backsplash for this stone. </span>');
                }else if (check[1] == 2){
                    chooseBacksplash('top');
                    jQuery('.cp-message p').prepend('<span class="cpalert">There is not enough backsplash for this stone. </span>');
                }else if (check[1] == 3){
                    chooseBacksplash('right');
                    jQuery('.cp-message p').prepend('<span class="cpalert">There is not enough backsplash for this stone. </span>');
                }
            }
        });
    }

    $scope.setLoadCart = function(){
        var baseurl =  jQuery(".baseurl").html();
        var ajaxurl = baseurl+'countertops/order/addtocart';
        var oldsku =  jQuery(".oldsku").html();
        var sinknum =  jQuery(".sinknum").html();
        var polishback =  jQuery(".polishback").html();
        var polishleft =  jQuery(".polishleft").html();
        var polishright =  jQuery(".polishright").html();
        var edgetype =  jQuery(".edgetype").html();
        var edgename = jQuery('.edgename').html();
        var backsplashback =  jQuery(".backsplashback").html();
        var backsplashleft =  jQuery(".backsplashleft").html();
        var backsplashright =  jQuery(".backsplashright").html();
        var slabsku =  jQuery(".skunum").html();
        var sinksku = jQuery(".sinksku").html();
        var sinktype = jQuery(".sinktype").html();
        var sink1dis = jQuery(".sink1dis").html();
        var sink2dis = jQuery(".sink2dis").html();
        var slabwidth =  jQuery(".shrink-width-span").html();
        var slabheight =  jQuery(".shrink-height-span").html();
        var vesselsize1 =  jQuery(".vesselsize1").html();
        var vessel1left =  jQuery(".vessel1left").html();
        var vessel1top =  jQuery(".vessel1top").html();                   
        var vesselsize2 =  jQuery(".vesselsize2").html();
        var vessel2left =  jQuery(".vessel2left").html();
        var vessel2top =  jQuery(".vessel2top").html();
        var faucettype =  jQuery(".faucettype").html();
        var faucet1degree =  jQuery(".faucet1degree").html();
        var faucet1dis =  jQuery(".faucet1dis").html();
        var faucet2degree =  jQuery(".faucet1degree").html();
        var faucet2dis =  jQuery(".faucet1dis").html();
        slabwidth = slabwidth.substring(0, slabwidth.length - 1);
        var slabheight =  jQuery(".shrink-height-span").html();
        slabheight = slabheight.substring(0, slabheight.length - 1);
        var sinknames = jQuery('.design-sinks span').html()

        $scope.totalsink = sinknum+','+sinksku+','+sinktype+','+sink1dis+','+sink1dis;
        $scope.totalfaucet = faucettype+','+faucet1degree+','+faucet1dis+','+faucet2degree+','+faucet2dis;
        $scope.totalpolish = polishleft+','+polishback+','+polishright;
        $scope.totalsplash = backsplashleft+','+backsplashback+','+backsplashright;
        $scope.totaledge = edgetype;
        $scope.totalvessel = vesselsize1+','+vesselsize2;
        $scope.slab = slabwidth+','+slabheight+','+slabsku;
        $scope.oldsku = oldsku;


        var request = $http({
            method: "post",
            dataType: 'json',
            url: ajaxurl,
            data: {
                slab: $scope.slab,
               sink: $scope.totalsink,
               faucet: $scope.totalfaucet,
               polish: $scope.totalpolish,
               backsplash: $scope.totalsplash,
               edging: $scope.totaledge,
               edgename:edgename,
               vessels: $scope.totalvessel,
               oldsku: $scope.oldsku
               
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });


        /* Check whether the HTTP Request is successful or not. */
        request.success(function (data) {
            //jQuery(".price").html(data);
            var errorcheck = data.split(',');
            if (errorcheck[0] == 2){
               jQuery(".price").html('<div class="error-message">'+errorcheck[1]+'</div>');
            }else{
                var url = baseurl+'checkout/cart/';
                window.location.replace(url);
            }
            
        });
    }

    $scope.setSample = function(sku, endcheck){
        if (typeof endcheck == 'undefined'){
            endcheck = '';
        }
        var baseurl =  jQuery(".baseurl").html();
        var ajaxurl = baseurl+'countertops/order/sample';
        $scope.sku = sku;
        var request = $http({
            method: "post",
            dataType: 'json',
            url: ajaxurl,
            data: {
                sku: $scope.sku
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });


        /* Check whether the HTTP Request is successful or not. */
        request.success(function (data) {
            
            if (data != 'stop' && endcheck == ''){
                jQuery('.cp-message2 p').prepend('<span class="cpalert">Your sample was added to the cart. </span>');
                jQuery('#'+sku+' .help .'+sku+' center').html('<b>sample added to cart</b>');
                //jQuery(".faucets").html(data);
            }else if (data != 'stop' && endcheck != ''){
                jQuery('.order-sample').html('sample add to cart');
            }else if (data == 'stop' && endcheck != ''){
                jQuery('.order-sample').html('no sample available'); 
            }
        });        
    }

    $scope.setTotals = function(){

        var baseurl =  jQuery(".baseurl").html();
        var sinknum =  jQuery(".sinknum").html();
        var polishback =  jQuery(".polishback").html();
        var polishleft =  jQuery(".polishleft").html();
        var polishright =  jQuery(".polishright").html();
        var edgetype =  jQuery(".edgetype").html();
        var edgename = jQuery('.edgename').html();
        var backsplashback =  jQuery(".backsplashback").html();
        var backsplashleft =  jQuery(".backsplashleft").html();
        var backsplashright =  jQuery(".backsplashright").html();
        var slabsku =  jQuery(".skunum").html();
        var slabname = jQuery('.design-stone span').html();
        var sinksku = jQuery(".sinksku").html();
        var sinktype = jQuery(".sinktype").html();
        var sink1dis = jQuery(".sink1dis").html();
        var sink2dis = jQuery(".sink2dis").html(); 
        var sinksize = jQuery('.sinksize').html();
        var faucettype = jQuery('.faucettype').html();
        var faucetdes = jQuery('.design-faucets span').html();
        var vesselsize1 =  jQuery(".vesselsize1").html();
        var vesselsize2 =  jQuery(".vesselsize2").html();
        var vessel1top =  jQuery(".vessel1top").html();
        var vessel1left =  jQuery(".vessel1left").html();
        var vessel2top =  jQuery(".vessel2top").html();
        var vessel2left =  jQuery(".vessel2left").html();  
        var faucet1degree =  jQuery(".faucet1degree").html(); 
        var faucet2degree =  jQuery(".faucet2degree").html();
        var faucet1dis =  jQuery(".faucet1dis").html();
        var faucet2dis =  jQuery(".faucet2dis").html();            
        var faucettype =  jQuery(".faucettype").html();
        var slabwidth =  jQuery(".shrink-width-span").html();
        slabwidth = slabwidth.substring(0, slabwidth.length - 1);
        var slabheight =  jQuery(".shrink-height-span").html();
        slabheight = slabheight.substring(0, slabheight.length - 1);
        var sinknames = jQuery('.design-sinks span').html();


        $scope.totalsink = sinknum+','+sinksku+','+sinktype+','+sink1dis+','+sink2dis+','+sinksize;
        $scope.totalvfaucet = faucet1degree+','+faucet1dis+','+faucet2degree+','+faucet2dis;
        $scope.totalfaucet = faucettype;
        $scope.totalpolish = polishleft+','+polishback+','+polishright;
        $scope.totalsplash = backsplashleft+','+backsplashback+','+backsplashright;
        $scope.totaledge = edgetype;
        $scope.totalvessel = vesselsize1+','+vesselsize2+','+vessel1top+','+vessel1left+','+vessel2top+','+vessel2left;
        $scope.slab = slabwidth+','+slabheight+','+slabsku;

        var ajaxurl = baseurl+'countertops/order/totals';

        var request = $http({
            method: "post",
            dataType: 'json',
            url: ajaxurl,
            data: {
                slab: $scope.slab,
                slabname: slabname,
                thickness: $scope.mythickness.size,
               sink: $scope.totalsink,
               sinknames: sinknames,
               vfaucet: $scope.totalvfaucet,
               faucet: $scope.totalfaucet,
               faucetdes: faucetdes,
               polish: $scope.totalpolish,
               backsplash: $scope.totalsplash,
               edging: $scope.totaledge,
               edgename:edgename,
               vessels: $scope.totalvessel
               
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });


        /* Check whether the HTTP Request is successful or not. */
        request.success(function (data) {
            jQuery(".price").html(data);

        var stonecolor = jQuery('.design-stone span').html();
        var totalprice = jQuery('.totalprice').html();
        var designurl = jQuery('.designurl').html();
        var fullurl = baseurl+'countertops/design/'+designurl;
        var sku = jQuery('.skunum').html();
        jQuery('.quoteamount').html(totalprice);
        var quoteamount = totalprice;
        var couponText = jQuery('.coupon-text').html();
        couponText = couponText.replace('<p>','');
        jQuery('#designsum').html('<a href="'+fullurl+'" target="_blank" style="color:#000">DESIGN SUMMARY</a>');
        jQuery('.next-tab').html('<div onclick="addToCart()" class="next-button">ADD TO CART</div><div class="quote-tab quoteme" onclick="quoteTab()" style="display: block;background-color: #808184;">EMAIL QUOTE</div></div><div class="quote-tab" onclick="backTab()" style="display: block;">BACK</div><br clear="left"><div class="quoteform" style="background-color: #808184;"><div class="qformcontain"><form id="emailForm" action="'+baseurl+'netsuite/postToNetsuite.php" method="post" ><input type="hidden" name="stonecolor" value="'+stonecolor+'"><input type="hidden" name="quoteamount" value="'+quoteamount+'"><input type="hidden" name="diagram" value="'+fullurl+'"><div class="qformemail">Email<br><input name="email" type="text" size="25" tabindex=3></div><div class="qformlaste">Last Name<br><input name="lastname" type="text" size="25" tabindex=2></div><div class="qformname">First Name<br><input name="firstname" type="text" size="25" tabindex=1></div><br clear="right"><div class="qformcoupon"><input id="includeCoupon" name="coupon" value="T" type="checkbox"> '+couponText+'</div><br clear="right"><div class="qformsubmit"><input type="submit" onclick="netsuite()" value="EMAIL QUOTE"></div></form></div></div>');


        });

    }

    /**
     * Manages the tabs
     *
     * @param {section_name} the tab that has been clicked 
     * @return display the tab clicked and related div
     */
     
    $scope.setMaster = function(section_name) {
 
        jQuery('.sinks-bowl1').css('display','none');
        jQuery('.sinks-bowl2').css('display','none');

        jQuery('.next-tab').html('<div onclick="nextTab()" class="next-button">NEXT</div><div class="back-button" onclick="backTab()" style="display: block;">BACK</div>');

        jQuery('.cpalert').remove();

        var checkstone = jQuery(".design-stone").html();
        var edgecheck = jQuery('.edgetype').html();
        

        if (section_name === 'EDGING'){
            if (checkstone.length > 1 ){
                $scope.ctab = {name: section_name};
                $scope.selected = section_name;
            }else{
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                return;
            }
        }else if (section_name === 'SINKS'){
            
            if (checkstone.length == 0 ){
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                return;
            }else if (edgecheck.length == 0){
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                return;
            }else{
                $scope.ctab = {name: section_name};
                $scope.selected = section_name;  
            }
        }else if (section_name === 'FAUCETS'){
            var totalpage = jQuery('.totalprice').html();
            var csinktype = jQuery('.design-sinks span').html();

            if (typeof csinktype != 'undefined'){
                csinktype = csinktype.split(" ");
            }

            if (typeof totalpage != 'undefined'){
                section_name = 'SINKS';
                $scope.ctab = {name: section_name};
                $scope.selected = section_name; 
            }else{
           
                if (jQuery('input[name=sinknum]:checked').val() != 0 && csinktype[1] == "undefined"){
                    
                        jQuery('.cp-message').prepend('<span class="cpalert">Please select a sink first. </span>');
                        jQuery('.cp-message2').prepend('<span class="cpalert">Please select a sink first. </span>');
                        return;                   
                }
                
                if (checkstone.length == 0 ){
                    
                    jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                    jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                    return;
                }else if (edgecheck.length == 0){
                    
                    jQuery('.cp-message p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                    jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                    return;
                }else{
                    
                    $scope.ctab = {name: section_name};
                    $scope.selected = section_name;  
                }
            
            }



        }else if (section_name === 'PRICE'){
            if (checkstone.length == 0 ){
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                return;
            }else if (edgecheck.length == 0){
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                return;
            }else{
                $scope.ctab = {name: section_name};
                $scope.selected = section_name;  
            }            
        }else{
            $scope.ctab = {name: section_name};
            $scope.selected = section_name;
        }



        
        if (section_name === 'DIMENSIONS'){
            jQuery(".back-button").css('display', 'none');
        }else{
            jQuery(".back-button").css('display', 'block'); 
        }
     // run ajax query
        if (section_name === 'STONE'){
        //align left side polish area to size of box
        var number = jQuery('input[name=slabheight]').val();
        var addme = ((number*8)-120)+34;
        jQuery('.cp-counter-left .edging').css('margin-top',addme);

            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1); 

            var baseurl =  jQuery(".baseurl").html()
            var ajaxurl = baseurl+'countertops/order/stones';

            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: $scope.mythickness.size,
                   slabwidth: slabwidth,
                   slabdepth: slabdepth,
                   
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {

                jQuery("#stone").html(data);
                reorderSlabs();
                var stone = jQuery('.skunum').html();
                if (stone){
                    slabClick(stone);
                }
                //$scope.allFilters($scope.mythickness.size);
                if ($scope.mythickness.size == 2){
                    var stone2 = jQuery('.filters2').html();
                    jQuery('#filters').html(stone2);   
                }else{
                     var stone3 = jQuery('.filters3').html();
                    jQuery('#filters').html(stone3);                     
                }
            });

        }

        if (section_name === 'EDGING'){

            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/edging';
            jQuery('.cp-counter-front').css('display','block');
            jQuery('.countertop-slab').css('height:322px');
            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: $scope.mythickness.size,
                   slabwidth: slabwidth,
                   slabdepth: slabdepth
                   
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {
                jQuery(".edgetypes").html(data);
                var number = jQuery('.edgechoice').html();
                    jQuery('.edge-under'+number).hide();
                jQuery('.edge-over'+number).show();
                jQuery('.edge-over'+number).addClass('active');
                jQuery('.ei'+number).addClass('tactive');
            });

        }

        if (section_name === 'SINKS'){
            jQuery('.cp-counter-front').css('display','block');
            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1);
            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/sinks';

            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   slabwidth: slabwidth,
                   slabdepth: slabdepth
                   
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {

                jQuery(".sinkbowltypes").html(data);
                jQuery('.loadingsinks').remove();
                jQuery('.sinks-number').css('display','block');
                var sinksku =  jQuery(".sinksku").html();
                var sinknum = jQuery('input[name=sinknum]:checked').val(); 
                var sinktype = jQuery('.sinktype').html();
                var remove = 0;



                if (sinknum == 0){
               
                    if (slabwidth > 54 && slabdepth > 21){
                        sinknum = 2;
                        jQuery('.1sinkok').css('display','block');
                        jQuery('input[name=sinknum]:eq(2)').click();
                    }else if (slabwidth <= 54 && slabdepth > 21){
                       sinknum = 1; 
                       jQuery('input[name=sinknum]:eq(1)').click();
                    }else if (slabdepth < 15.75){
                        jQuery('#onesink').css('display', 'none');
                        jQuery('#twosinks').css('display', 'none');
                    }

                   
                }

                if (slabwidth < 31.75){
                    jQuery('#twosinks').css('display','none');
                }

                var vessel1left = jQuery('.vessel1left').html();
                var vessel1top = jQuery('.vessel1top').html();
                var vesselsize1 = jQuery('.vesselsize1').html();

                var vessel2left = jQuery('.vessel2left').html();
                var vessel2top = jQuery('.vessel2top').html();
                var vesselsize2 = jQuery('.vesselsize2').html();


                if (sinknum == 1){

                    if (sinktype == 'Vessel'){

                        jQuery('.sink1-display').css('display','block');
                        jQuery('.sinks-bowls').css('display','block');
                        jQuery('.sinks-vessel1').css('display','block');
                        jQuery('.sinks-bowl1').css('display','none');   
                        jQuery('.vessel1-distance').val(vessel1left);
                        jQuery('.bowl1-distance-top').val(vessel1top);

                        jQuery('.bowl1-distance-width').val(vesselsize1);
                        restoreVessel();

                        //chooseSink(sinksku,'Vessel','center');                                             
                    }else{
                        jQuery('.sinks-bowls').css('display','block');
                        jQuery('.sinks-bowl1').css('display','block');
                    }
                    jQuery('.1sinkok').css('display','block');
                    jQuery('.1sinkok .sink-image'+sinksku).addClass('sink-selected');
                }
                if (sinknum == 2){
                    if (sinktype == 'Vessel'){
                        jQuery('.sink1-lefthole').css('display','none');
                        jQuery('.sink1-middlehole').css('display','none');
                        jQuery('.sink1-righthole').css('display','none');                        
                        jQuery('.sink2-lefthole').css('display','none');
                        jQuery('.sink2-middlehole').css('display','none');
                        jQuery('.sink2-righthole').css('display','none'); 
                        jQuery('.sink1-display').css('display','block');
                        jQuery('.sink2-display').css('display','block');                    
                        jQuery('.sinks-bowls').css('display','block');
                        jQuery('.sinks-vessel1').css('display','block');
                        jQuery('.sinks-vessel2').css('display','block');
                        jQuery('.sinks-bowl1').css('display','none');
                        jQuery('.sinks-bowl2').css('display','none');
                        jQuery('.vessel1-distance').val(vessel1left);
                        jQuery('.bowl1-distance-top').val(vessel1top);
                        jQuery('.bowl1-distance-width').val(vesselsize1); 
                        jQuery('.vessel2-distance').val(vessel2left);
                        jQuery('.bowl2-distance-top').val(vessel2top);
                        jQuery('.bowl2-distance-width').val(vesselsize2); 

                        restoreVessel();
                        //chooseSink(sinksku,'Vessel','center');                                                                            
                    }else{
                        jQuery('.sinks-bowls').css('display','block');
                        jQuery('.sinks-bowl1').css('display','block');
                        jQuery('.sinks-bowl2').css('display','block');
                    }
                    
                    jQuery('.2sinkok').css('display','block');
                    jQuery('.2sinkok .sink-image'+sinksku).addClass('sink-selected');
                }

                var faucettype = jQuery('.faucettype').html();
                if (faucettype.length > 0 && sinktype != 'Vessel'){
                    holeClick(faucettype);
                }

                if (section_name === 'PRICE'){
                    
                }
                
            });

            var slabwidth = jQuery('.shrink-width-span').html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1); 
           // slabwidth = slabwidth[0];

            var slabheight = jQuery('.shrink-height-span').html();
            slabheight = slabheight.substring(0, slabheight.length - 1); 
            //slabheight = slabheight[0];

            var sinkinfowidth = jQuery('.sinkinfowidth').html();
            sinkinfowidth = sinkinfowidth.replace('<p>','');
            sinkinfowidth = sinkinfowidth.replace('</p>','');
            sinkinfowidth =  sinkinfowidth.split('#');

            var sinkinfodepth = jQuery('.sinkinfodepth').html();
            sinkinfodepth = sinkinfodepth.replace('<p>','');
            sinkinfodepth = sinkinfodepth.replace('</p>','');
            sinkinfodepth =  sinkinfodepth.split('#');

            // Your depth of #" can accommodate: # sinks
            if (slabwidth > 54 && slabheight > 21){
                    var textinfo =  sinkinfowidth[0]+' <b>'+slabwidth+'"</b> '+sinkinfowidth[1]+' <b>2 sinks</b>';
                    $scope.myData = {textf: textinfo};
                    
            }else if (slabwidth > 21 && slabwidth <= 54 && slabheight > 21){
                var textinfo = sinkinfowidth[0]+' <b>'+slabwidth+'"</b> '+sinkinfowidth[1]+' <b>1 sink</b>';
                $scope.myData = {textf: textinfo};
                
            }else if (slabheight < 15.75){
                var textinfo = sinkinfodepth[0]+' <b>'+slabheight+'"</b> '+sinkinfodepth[1]+' <b>0 sinks</b>';
                $scope.myData = {textf: textinfo};
                
            }else{
                var textinfo = 'Please choose the number of sinks</b>';
                $scope.myData = {textf: textinfo};
                
            }




        }      

        if (section_name === 'FAUCETS'){
            jQuery('.toggle-content.tabs > ul:nth-child(1) > li:nth-child(6)').css('display','block');
            var sinknum = jQuery('.sinknum').html();
            if (sinknum == '' || sinknum == '0' ){
                section_name = "PRICE";
            }                
        }

        if (section_name === 'FAUCETS'){
            jQuery('.toggle-content.tabs > ul:nth-child(1) > li:nth-child(6)').css('display','block');
            var sinks = Number(jQuery('.sinknum').html());
            var holeheight = jQuery('.sink1-display').css('height');
            holeheight = removepx(holeheight);
            var holeheight2 = jQuery('.sink2-display').css('height');
            holeheight2 = removepx(holeheight2); 
                     
            holeheight = roundme(((holeheight/2)+28)/8);
            holeheight2 = roundme(((holeheight2/2)+28)/8);

            if(jQuery('.sink1-display').hasClass('vessel')){
                jQuery('.design-faucets').html('');


                // set faucet distance 1" beyond vessel hole
                holeheight = parseFloat(jQuery('.vesselsize1').html());
                if (holeheight >= 7){
                    holeheight = holeheight+1;
                }else{
                    holeheight = '4.50';
                }

                $scope.faucet1 = holeheight;
                $scope.showVessels = true;
                $scope.showHoles = false;

                // set faucet distance 1" beyond vessel hole
                holeheight2 = parseFloat(jQuery('.vesselsize2').html());
                if (holeheight2 >= 7){
                    holeheight2 = holeheight2+1;
                }else{
                    holeheight2 = '4.50';
                }
                  
                if (sinks == 2){
                    
                    $scope.faucet2 = holeheight2;
                    $scope.showVessels2 = true;
                }else{
                    $scope.showVessels2 = false; 
                }
            }else{
                $scope.showHoles = true;
                $scope.showVessels = false;
                $scope.showVessels2 = false;              
            }

            // restore vessel faucets
            var faucet1dis = jQuery('.faucet1dis').html();
            if (typeof faucet1dis != 'undefined' && faucet1dis != ''){
                $scope.faucet1 = faucet1dis;
                var faucet1degree = jQuery('.faucet1degree').html();
                
                if (faucet1degree == 1){
                    jQuery('.left45').css('display', 'block');
                }else if (faucet1degree == 2){
                    jQuery('.center1').css('display', 'block');
                }else{
                    jQuery('.right45').css('display', 'block');
                }
            }
   
            var faucet2dis = jQuery('.faucet2dis').html();
            if (typeof faucet2dis != 'undefined' && faucet2dis != ''){
                $scope.faucet2 = faucet2dis;
                var faucet2degree = jQuery('.faucet2degree').html();
                
                if (faucet2degree == 1){
                    jQuery('.left452').css('display', 'block');
                }else if (faucet1degree == 2){
                    jQuery('.center2').css('display', 'block');
                }else{
                    jQuery('.right452').css('display', 'block');
                }
            }

            //jQuery('.next-tab').html('<div onclick="nextTab(\'total\')" class="next-button">NEXT</div><div class="back-button" onclick="backTab()" style="display: block;">BACK</div>');
        }
    }

    $scope.isSelected = function(section_name) {
        
        if ($scope.start == 1){
            $scope.selected = 'DIMENSIONS';
            $scope.start = 0;
        }

        return $scope.selected === section_name;
    }   

// reload content

$scope.edgingReady = function() {
    var polishback = jQuery('.polishback').html();
    var polishleft = jQuery('.polishleft').html();
    var polishright = jQuery('.polishright').html();
    var backsplashback = jQuery('.backsplashback').html();
    var backsplashleft = jQuery('.backsplashleft').html();
    var backsplashright = jQuery('.backsplashright').html();    
    var edgetype = jQuery('.edgetype').html();
    var number = jQuery('.edgechoice').html();


    if (backsplashback == 1){
        chooseBacksplash('top');
    }

    if (backsplashleft == 1){
        chooseBacksplash('left');
    }

    if (backsplashright == 1){
        chooseBacksplash('right');
    }  

    if (polishback == 1){
        choosePolish('top');
    }

    if (polishleft == 1){
        choosePolish('left');
    }

    if (polishright == 1){
        choosePolish('right');
    }


    jQuery('.edge-under'+number).hide();
    jQuery('.edge-over'+number).show();
    jQuery('.edge-over'+number).addClass('active');
    jQuery('.ei'+number).addClass('tactive');

    //edgeselect(edgechoice,'',edgetype);
}

 $scope.sinkReady = function() {

    jQuery(".bowl1-distance").numeric();
    jQuery(".bowl2-distance").numeric();
    jQuery(".vessel1-distance").numeric();
    jQuery(".vessel2-distance").numeric();
    jQuery(".bowl1-distance-top").numeric();
    jQuery(".bowl2-distance-top").numeric();
    jQuery(".bowl1-distance-width").numeric();
    jQuery(".bowl2-distance-width").numeric();
    
    // manage limits
    var slabheight = jQuery('.shrink-height-span').html();
    slabheight = slabheight.substring(0, slabheight.length - 1);
    var slabwidth = jQuery('.shrink-width-span').html();
    slabwidth = slabwidth.substring(0, slabwidth.length - 1);


    var sinknum = jQuery('.sinknum').html();
    var sinksku = jQuery('.sinksku').html();
    
    if (sinksku == 'LAB-CUT-CUS'){



        
    }else if (sinksku){
        jQuery('input:radio[name=sinknum]').val([sinknum]);

        var sink1dis = jQuery('.sink1dis').html();
        var sink2dis = jQuery('.sink2dis').html();
        jQuery('.bowl1-distance').val(sink1dis);
        jQuery('.bowl2-distance').val(sink2dis);
    }

    var sinktype = jQuery('.sinktype').html();
    // reload vessel data
    if (sinktype == 'Vessel'){
        jQuery('.sinks-bowl1').css('display','none');
        jQuery('.sinks-bowl2').css('display','none');

    }

    if (sinksku == 'LAB-CUT-CUS'){
        restoreVessel();
    }else{

        if (sinksku){

            //chooseSink(sinksku, sinktype);
        }
    }
 }

$scope.faucetReady = function() {
    jQuery(".faucet1-distance").numeric();
    jQuery(".faucet2-distance").numeric();
    var sinksku = jQuery('.sinksku').html();
    if (sinksku == 0){
        var scope = angular.element(document.getElementById("cp-controller")).scope();
        scope.$apply(function () {
           scope.setMaster('PRICE');
        });
    }else{
        var sinktype = jQuery('.sinktype').html();
        if (sinktype != 'Vessel'){
            var faucettype = jQuery('.faucettype').html();
            if (faucettype.length > 0){
                holeClick(faucettype);
            }
        }
        var deg1 = jQuery('.faucet1degree').html();
        var deg2 = jQuery('.faucet1degree').html();

        if (deg1 == ''){
            jQuery(".degree1-none").removeClass('degreehover');
            jQuery(".degree1-none").css("background-color","#199CA3");
            jQuery(".degree1-none").css("color","#F1FFBC");
            jQuery(".degree1-none").css("border","1px solid #F1FFBC");         
        }

        if (deg1 == 1){
            jQuery(".degree1-left").removeClass('degreehover');
            jQuery(".degree1-left").css("background-color","#199CA3");
            jQuery(".degree1-left").css("color","#F1FFBC");
            jQuery(".degree1-left").css("border","1px solid #F1FFBC");         
        }

        if (deg1 == 2){
            jQuery(".degree1-center").removeClass('degreehover');
            jQuery(".degree1-center").css("background-color","#199CA3");
            jQuery(".degree1-center").css("color","#F1FFBC");
            jQuery(".degree1-center").css("border","1px solid #F1FFBC");         
        }

        if (deg1 == 3){
            jQuery(".degree1-right").removeClass('degreehover');
            jQuery(".degree1-right").css("background-color","#199CA3");
            jQuery(".degree1-right").css("color","#F1FFBC");
            jQuery(".degree1-right").css("border","1px solid #F1FFBC");         
        }

        if (deg2 == ''){
            jQuery(".degree2-none").removeClass('degreehover');
            jQuery(".degree2-none").css("background-color","#199CA3");
            jQuery(".degree2-none").css("color","#F1FFBC");
            jQuery(".degree2-none").css("border","1px solid #F1FFBC");         
        }


        if (deg2 == 1){
            jQuery(".degree2-left").removeClass('degreehover');
            jQuery(".degree2-left").css("background-color","#199CA3");
            jQuery(".degree2-left").css("color","#F1FFBC");
            jQuery(".degree2-left").css("border","1px solid #F1FFBC");                
        }

        if (deg2 == 2){
            jQuery(".degree2-center").removeClass('degreehover');
            jQuery(".degree2-center").css("background-color","#199CA3");
            jQuery(".degree2-center").css("color","#F1FFBC");
            jQuery(".degree2-center").css("border","1px solid #F1FFBC");                
        }

        if (deg2 == 3){
            jQuery(".degree2-right").removeClass('degreehover');
            jQuery(".degree2-right").css("background-color","#199CA3");
            jQuery(".degree2-right").css("color","#F1FFBC");
            jQuery(".degree2-right").css("border","1px solid #F1FFBC");                
        }
        
    }


}

$scope.priceReady = function() {
    nextTab('total');
}

  $scope.pageTop = function(thickness,price,color,type,veins,tones,page,page2){

            
            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1); 
            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/pagetop';
            jQuery('#loadmore').after('<center id="spinner1"><img src="'+baseurl+'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center>');
            jQuery('#loadmore').remove();
            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: thickness,
                   price:price,
                   color:color,
                   type:type,
                   veins:veins,
                   tones:tones,
                   slabwidth:slabwidth,
                   slabdepth:slabdepth,
                   page:page,
                   page2:page2,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {
                jQuery("#stone .top-slabs .slab-images").append(data);
                reorderSlabs();
                var stop = jQuery("#stop").html();
                jQuery('#spinner2').remove();
                if (stop == 1){
                    jQuery('#loadmore').remove();
                }
                
            });
    }

  $scope.pageBottom = function(thickness,price,color,type,veins,tones,page,page2){
            $scope.mythickness.size = thickness;
            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1); 
            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/pagebottom';
            jQuery('#loadmore2').after('<center id="spinner2"><img src="'+baseurl+'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center>');
            jQuery('#loadmore2').remove();
            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: thickness,
                   price:price,
                   color:color,
                   type:type,
                   veins:veins,
                   tones:tones,
                   slabwidth:slabwidth,
                   slabdepth:slabdepth,
                   page:page,
                   page2:page2,
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {
                jQuery("#stone .fullslabs .slab-images").append(data);
                reorderSlabs();
                var stop = jQuery("#stop2").html();
                jQuery('#spinner2').remove();
                if (stop == 1){
                    jQuery('#loadmore2').remove();
                }
                //$scope.allFilters(thickness,price,color,type,veins,tones,remenants);
            });
    }

  $scope.filterSlabs = function(thickness,price,color,type,veins,tones,remenants,page,page2, previouspage, previouspage2){
            $scope.mythickness.size = thickness;
            var slabwidth = jQuery(".shrink-width-span").html();
            slabwidth = slabwidth.substring(0, slabwidth.length - 1);
            var slabdepth = jQuery(".shrink-height-span").html();
            slabdepth = slabdepth.substring(0, slabdepth.length - 1); 
            var baseurl =  jQuery(".baseurl").html();
            var ajaxurl = baseurl+'countertops/order/filter';

            var request = $http({
                method: "post",
                dataType: 'json',
                url: ajaxurl,
                data: {
                   width: $scope.mywidth,
                   height: $scope.myheight,
                   thickness: thickness,
                   price:price,
                   color:color,
                   type:type,
                   veins:veins,
                   tones:tones,
                   remenants:remenants,
                   slabwidth:slabwidth,
                   slabdepth:slabdepth,
                   page:page,
                   page2:page2,
                   previouspage:previouspage,
                   previouspage2:previouspage2
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Check whether the HTTP Request is successful or not. */
            request.success(function (data) {
                jQuery("#stone").html(data);
                reorderSlabs();
                $scope.allFilters(thickness,price,color,type,veins,tones,remenants,'filter');
            });
    }


}





//********************************************************************************
// Allows us to detect when a tab is loaded, so we can populate it.
//********************************************************************************

project.directive('whenReady', ['$interpolate', function($interpolate) {
  return {
    restrict: 'A',
    priority: Number.MIN_SAFE_INTEGER, // execute last, after all other directives if any.
    link: function($scope, $element, $attributes) {
      var expressions = $attributes.whenReady.split(';');
      var waitForInterpolation = false;

      function evalExpressions(expressions) {
        expressions.forEach(function(expression) {
          $scope.$eval(expression);
        });
      }

      if ($attributes.whenReady.trim().length == 0) { return; }

      if (expressions.length > 1) {
        if ($scope.$eval(expressions.pop())) {
          waitForInterpolation = true;
        }
      }

      if (waitForInterpolation) {
        requestAnimationFrame(function checkIfInterpolated() {
          if ($element.text().indexOf($interpolate.startSymbol()) >= 0) { // if the text still has {{placeholders}}
            requestAnimationFrame(checkIfInterpolated);
          }
          else {
            evalExpressions(expressions);
          }
        });
      }
      else {
        evalExpressions(expressions);
      }
    }
  }
}]);

//********************************************************************************
// blur directive
//********************************************************************************

project.directive('ngBlur', ['$parse', function($parse) {
  return function(scope, element, attr) {
    var fn = $parse(attr['ngBlur']);
    element.bind('blur', function(event) {
      scope.$apply(function() {
        fn(scope, {$event:event});
      });
    });
  }
}]);



//********************************************************************************
// Needed to handle Math.round as Angular doesn't load the javascript Math funtion
//********************************************************************************

function roundme(number){
    number = (Math.round(number * 4) / 4).toFixed(2);
    var end = number.split('.');
    if (end[1] == 0){
        number = end[0];
    }
    return number;
}

function removepx(number){
    number = number.split("px");
    number = Number(number[0]);
    return number;
}

// load more stones
function loadMore(size, page, page2, price, color, type, veins, tones){

        var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.pageTop(size, price, color, type, veins, tones ,page, page2);
    });
}

// load more stones
function loadMore2(size, page, page2, price, color, type, veins, tones){

        var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.pageBottom(size, price, color, type, veins, tones, page, page2);
    });
}

//********************************************************************************
// Help bubbles
//********************************************************************************

function infoOver(name, left, prefix){
    var classname = '.help-'+name;
    var fullclassname = '.'+prefix+'-'+name+' .help .help-'+name;
    var classheight = jQuery(fullclassname).css('height');
    classheight = removepx(classheight);
    classheight = classheight+20;

    jQuery(classname).css('top', '-'+classheight+'px');
    jQuery(classname).css('left', left+'px');
    jQuery(classname).show();
}

function infoOut(name){
    jQuery('.help-'+name).hide();
}

function helpOver(upperdiv, name, prefix, left, height){
    var classname = '.help-'+name;
    var fullclassname = '.'+prefix+'-'+upperdiv+' .help .help-'+name;
    var classheight = jQuery(fullclassname).css('height');
    classheight = removepx(classheight);
    classheight = classheight+height;

    jQuery(classname).css('top', '-'+classheight+'px');
    jQuery(classname).css('left', left+'px');
    jQuery(classname).show();
}

function selectthick(number){
    if (number == 2){
        jQuery('input:radio[name=thickness]:eq(0)').click(); 
    }else{
        jQuery('input:radio[name=thickness]:eq(1)').click();
    }
    
    jQuery('.design-thickness').html('<span style="color:#000">THICKNESS: </span>'+number+'cm');

    var restore = jQuery('.rthickness').html();

    if (restore == ''){
        jQuery('.design-stone').html('');
    }
    jQuery('.skunum').html('');

    var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.thickness(number);
    });
      
}

function slabHoverShow(sku){
    jQuery("."+sku).show();
}

function slabHoverHide(sku){
    jQuery("."+sku).hide();
}

function holeHover(name){
    holeImageHover(name);
   jQuery("."+name).css('background-color','#199CA3'); 
}

function holeOut(name){
    if (jQuery("."+name).is('.selected')) { 
    }else{ 
        jQuery("."+name).css('background-color',''); 
        var baseurl = jQuery('.site-base').html();

        if (name == 'holes-none'){
            jQuery('.'+name+' img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac1.png');
        }else if (name == 'holes-one'){
            jQuery('.'+name+' img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac2.png');
        }else if (name == 'holes-two'){
            jQuery('.'+name+' img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac3.png');
        }else if (name == 'holes-three'){
            jQuery('.'+name+' img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac4.png');

        }

    }

}

function holeImageHover(name){
    var imgsrc = '';
    var baseurl = jQuery('.site-base').html();

    if (name == 'holes-none'){
        imgsrc = 'fac1over.png';
    }else if (name == 'holes-one'){
        imgsrc = 'fac2over.png';
    }else if (name == 'holes-two'){
        imgsrc = 'fac3over.png';
    }else{
        imgsrc = 'fac4over.png';
    }
    jQuery('.'+name+' img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/'+imgsrc);
}

function holeClick(name, designtext){
    var sinktype = jQuery('.sinktype').html();
    var rectangle = sinktype.search('Square');

    jQuery('.faucettype').html(name);
    jQuery('.design-sinks').css('color', '#000');
    jQuery('.design-sinks b').css('color', '#000');
    jQuery(".holy").removeClass('selected');
    jQuery(".holy").css('background-color','');
    jQuery("."+name).css('background-color','#199CA3'); 
    jQuery("."+name).addClass('selected');
    var sinknum = jQuery(".sinknum").html();
    var sink1width = jQuery(".sink1-display").css('width');
    sink1width = removepx(sink1width);
    var half1sink = (sink1width/2)-10;
    var sink2display = jQuery(".sink2-display").css('display');
     var baseurl = jQuery('.site-base').html();

        jQuery('.holes-none img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac1.png');
        jQuery('.holes-one img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac2.png');
        jQuery('.holes-two img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac3.png');
        jQuery('.holes-three img').attr('src', baseurl+'skin/frontend/rwd/default/images/countertops/fac4.png');

    holeImageHover(name);

    jQuery(".sink1-middlehole").css('display','none');
    jQuery(".sink1-lefthole").css('display','none'); 
    jQuery(".sink1-righthole").css('display','none');

    jQuery(".sink2-middlehole").css('display','none');
    jQuery(".sink2-lefthole").css('display','none'); 
    jQuery(".sink2-righthole").css('display','none');

    if  (name != 'holes-none'){
        jQuery(".sink1-middlehole").css('display','block');
        jQuery(".sink1-middlehole").css('left',half1sink+'px');
        jQuery(".sink1-middlehole").css('top','-22px');
    

        if (sinknum == 2){
            jQuery(".sink2-middlehole").css('display','block');
            jQuery(".sink2-middlehole").css('left',half1sink+'px');
            jQuery(".sink2-middlehole").css('top','-22px');            
        }

        if (name == 'holes-one'){
            jQuery('.design-faucets').html("<b>FAUCETS:</b> <span>1 hole</span");
        }

        if  (name == 'holes-two'){
            jQuery('.design-faucets').html('<b>FAUCETS:</b> <span>3 holes 4" spread</span>');
            var leftside = half1sink-18;
            jQuery(".sink1-lefthole").css('display','block');            
            jQuery(".sink1-lefthole").css('left',leftside+'px');
            jQuery(".sink1-lefthole").css('top','-18px');

            var rightside = half1sink+18;
            jQuery(".sink1-righthole").css('display','block');            
            jQuery(".sink1-righthole").css('left',rightside+'px');
            jQuery(".sink1-righthole").css('top','-18px'); 

            if (sinknum == 2){
                jQuery(".sink2-lefthole").css('display','block');            
                jQuery(".sink2-lefthole").css('left',leftside+'px');
                jQuery(".sink2-lefthole").css('top','-18px'); 

                jQuery(".sink2-righthole").css('display','block');            
                jQuery(".sink2-righthole").css('left',rightside+'px');
                jQuery(".sink2-righthole").css('top','-18px');                           
            }                       
        }else if (name == 'holes-three'){
            jQuery('.design-faucets').html('<b>FAUCETS:</b> <span>3 holes 8" spread</span>');
            var leftside = half1sink-28;
            jQuery(".sink1-lefthole").css('display','block');            
            jQuery(".sink1-lefthole").css('left',leftside+'px');
            jQuery(".sink1-lefthole").css('top','-18px');

            var rightside = half1sink+28;
            jQuery(".sink1-righthole").css('display','block');            
            jQuery(".sink1-righthole").css('left',rightside+'px');
            jQuery(".sink1-righthole").css('top','-18px'); 

            if (sinknum == 2){
                jQuery(".sink2-lefthole").css('display','block');            
                jQuery(".sink2-lefthole").css('left',leftside+'px');
                jQuery(".sink2-lefthole").css('top','-18px'); 

                jQuery(".sink2-righthole").css('display','block');            
                jQuery(".sink2-righthole").css('left',rightside+'px');
                jQuery(".sink1-righthole").css('top','-18px'); 
            }
        }
    }else{
        jQuery('.design-faucets').html('');
    }

     if (rectangle > 0){
        jQuery('.sink1-lefthole').css('top','-22px');
        jQuery('.sink1-righthole').css('top','-22px');
        jQuery('.sink2-lefthole').css('top','-22px');
        jQuery('.sink2-righthole').css('top','-22px');        
    }else{
         jQuery('.sink1-lefthole').css('top','-18px');
        jQuery('.sink1-righthole').css('top','-18px'); 
         jQuery('.sink2-lefthole').css('top','-18px');
        jQuery('.sink2-righthole').css('top','-18px');              
    }

}

function faucetClick(name){
    var classnames = ["degree1-left", "degree1-center", "degree1-right", "degree1-none"];

    var dfcheck = jQuery('.design-faucets').html();
    var df1check = jQuery('.faucet2degree').html();

    if (dfcheck == '' && name == 'degree1-none' || df1check == '' && name == 'degree1-none'){
        jQuery('.design-faucets').html("");
        jQuery('.faucettype').html('');
    }else if (dfcheck != '' && name == 'degree1-none' || dfcheck == '' && name != 'degree1-none'){
        jQuery('.design-faucets').html("<b>FAUCETS:</b> <span>1 hole</span>");
        jQuery('.faucettype').html('holes-one');
    }else if (dfcheck != '' && name != 'degree1-none'){    
        jQuery('.design-faucets').html("<b>FAUCETS:</b> <span>2 holes</span>");
        jQuery('.faucettype').html('holes-one');
    }

    for (var i = 0; i < classnames.length; i++){
        jQuery("."+classnames[i]).addClass('degreehover');
        jQuery("."+classnames[i]).css("background-color","#a0d6d9");
        jQuery("."+classnames[i]).css("color","#000");
        jQuery("."+classnames[i]).css("border","1px solid #fff");
    }
    // check if hole is greater than 1.75
    var vesselsize1 = jQuery('.vesselsize1').html();
    if (vesselsize1 == '1.75'){
         jQuery('.centerhole1').css('border','0px');
    }else{
        jQuery('.centerhole1').css('border','2px solid #a6a8ab');
    }
    jQuery('.centerhole1').css('display','block');

    jQuery('.center1').css('top','');
    jQuery('.center1').css('left','');
    jQuery('.center1').css('height','');
    jQuery('.sinkholeright1').css('top','');
    jQuery('.sinkholeright1').css('left','');
    jQuery('.sinkholeleft1').css('top','');
    jQuery('.sinkholeleft1').css('left','');
    jQuery('.right45 .line1').css('width',''); 
    jQuery('.left45 .line1').css('width','');
    jQuery('.left45 .line1').css('height','');  
    jQuery('.left45').css('top','');  
    jQuery('.left45').css('left','');    



    var sinks = Number(jQuery('.sinknum').html());

    var holeheight = jQuery('.sink1-display').css('height');
    holeheight = removepx(holeheight);
    var holeheight2 = jQuery('.sink2-display').css('height');
    holeheight2 = removepx(holeheight2); 

    holeheight = holeheight/8;
    holeheight2 = holeheight2/8;

    // put the hole 1" beyond the bowl

    if (holeheight > 2.5){
        holeheight = roundme((holeheight/2)+1);   
    }else{
        holeheight = 4.5; 
    }
 
    if (holeheight2 > 2.5){
        holeheight2 = roundme((holeheight2/2)+1);   
    }else{
        holeheight = 4.5; 
    }

    jQuery('.faucet1-distance').val(holeheight);
    jQuery('.lineinfo1').css('display','block');
    jQuery('.lineinfo1').html(holeheight+'"');

    var holelen = holeheight.toString().length;
    holelen = (holelen*4)+14;

    if (name == 'degree1-right'){
        jQuery('.lineinfo1').css('top','-'+10+'px');
        jQuery('.lineinfo1').css('left','-'+holelen+'px');

    }else{
        jQuery('.lineinfo1').css('top','-'+10+'px');
        jQuery('.lineinfo1').css('left',18+'px');
    }






    jQuery("."+name).removeClass('degreehover');
    jQuery("."+name).css("background-color","#199CA3");
    jQuery("."+name).css("color","#F1FFBC");
    jQuery("."+name).css("border","1px solid #F1FFBC");
    
   if (name == 'degree1-left'){
        jQuery(".right45").css("display","none");
        jQuery(".center1").css("display","none");
        jQuery(".left45").css("display","block");
        jQuery('.faucet1degree').html(1);
        var middle = jQuery('.sink1-display').css('height');   
   }else{
        jQuery(".left45").css("display","none");
 
   }

   if (name == 'degree1-center'){
        // define line height
        jQuery('.center1').css('height', holeheight*8+'px');
        var moveholeup = ((holeheight-1.75)*8)+10;
        jQuery('.center1').css('top', '-'+moveholeup+'px');

        jQuery('.faucet1degree').html(2);
        jQuery(".center1").css("display","block");
   }else{
        jQuery(".center1").css("display","none");
   } 

   if (name == 'degree1-right'){


    
        jQuery('.faucet1degree').html(3);
        jQuery(".right45").css("display","block");
   }else{
        jQuery(".right45").css("display","none");
        
   }

    

   jQuery('.faucet1dis').html(holeheight);
   if (name == 'degree1-none'){
     jQuery('.faucet1dis').html('');
     jQuery('.faucet1degree').html('');
   }
}

function faucetClick2(name){
    var classnames = ["degree2-left", "degree2-center", "degree2-right", "degree2-none"];
    var dfcheck = jQuery('.design-faucets').html();
    var df2check = jQuery('.faucet1degree').html();
    
    if (dfcheck == '' && name == 'degree2-none' || df2check == '' && name == 'degree2-none'){
        jQuery('.design-faucets').html("");
        jQuery('.faucettype').html('');
    }else if (dfcheck != '' && name == 'degree2-none' || dfcheck == '' && name != 'degree2-none'){
        jQuery('.design-faucets').html("<b>FAUCETS:</b> <span>1 hole</span>");
        jQuery('.faucettype').html('holes-one');
    }else if (dfcheck != '' && name != 'degree2-none'){    
        jQuery('.design-faucets').html("<b>FAUCETS:</b> <span>2 holes</span>");
        jQuery('.faucettype').html('holes-one');
    }


    for (var i = 0; i < classnames.length; i++){
        jQuery("."+classnames[i]).addClass('degreehover');
        jQuery("."+classnames[i]).css("background-color","#a0d6d9");
        jQuery("."+classnames[i]).css("color","#000");
        jQuery("."+classnames[i]).css("border","1px solid #fff");
    }

    var vesselsize2 = jQuery('.vesselsize2').html();
    if (vesselsize2 == '1.75'){
         jQuery('.centerhole2').css('border','0px');
    }else{
        jQuery('.centerhole2').css('border','2px solid #a6a8ab');
    }
    jQuery('.centerhole2').css('display','block');

    jQuery('.center2').css('top','');
    jQuery('.center2').css('left','');
    jQuery('.center2').css('height','');
    jQuery('.sinkholeright2').css('top','');
    jQuery('.sinkholeright2').css('left','');
    jQuery('.sinkholeleft2').css('top','');
    jQuery('.sinkholeleft2').css('left','');
    jQuery('.right452 .line1').css('width',''); 
    jQuery('.left452 .line1').css('width','');
    jQuery('.left452 .line1').css('height','');  
    jQuery('.left452').css('top','');  
    jQuery('.left452').css('left',''); 

    // set center height
    var holeheight2 = jQuery('.sink2-display').css('height');
    //alert('ww1' - holeheight2);
    holeheight2 = removepx(holeheight2); 
    holeheight2 = holeheight2/8;

    if (name == 'degree2-right'){
        jQuery('.lineinfo2').css('top','-'+8+'px');
        jQuery('.lineinfo2').css('left','-'+36+'px');
    }else{
        jQuery('.lineinfo2').css('top','-'+8+'px');
        jQuery('.lineinfo2').css('left',18+'px');
    }
    
    if (holeheight2 > 2.5){
        holeheight2 = roundme((holeheight2/2)+1);   
    }else{
        holeheight2 = 4.5;
    }

   var holelen = holeheight2.toString().length;
    holelen = (holelen*4)+14;

    if (name == 'degree2-right'){
        jQuery('.lineinfo2').css('top','-'+10+'px');
        jQuery('.lineinfo2').css('left','-'+holelen+'px');

    }else{
        jQuery('.lineinfo2').css('top','-'+10+'px');
        jQuery('.lineinfo2').css('left',18+'px');
    }

    jQuery('.faucet2-distance').val(holeheight2);
    jQuery('.lineinfo2').css('display','block');
    jQuery('.lineinfo2').html(holeheight2+'"');

    jQuery('.center2').css('height', holeheight2*8+'px');
    var moveholeup = ((holeheight2-1.75)*8)+10;
    jQuery('.center2').css('top', '-'+moveholeup+'px');

    jQuery("."+name).removeClass('degreehover');
    jQuery("."+name).css("background-color","#199CA3");
    jQuery("."+name).css("color","#F1FFBC");
    jQuery("."+name).css("border","1px solid #F1FFBC");
    
   if (name == 'degree2-left'){
        jQuery(".left452").css("display","block");
        jQuery('.faucet2degree').html(1);

   }else{
        jQuery(".left452").css("display","none");

   }
   if (name == 'degree2-center'){

        jQuery(".center2").css("display","block");
        jQuery('.faucet2degree').html(2);
   }else{
        jQuery(".center2").css("display","none");
   } 

   if (name == 'degree2-right'){
        jQuery(".right452").css("display","block");
        jQuery('.faucet2degree').html(3);
   }else{
        jQuery(".right452").css("display","none");

   } //alert('ww2');
    var faucet2dis = jQuery('.faucet2-distance').val();
    jQuery('.faucet2dis').html(faucet2dis);
    if (name == 'degree2-none'){
     jQuery('.faucet2dis').html('');
     jQuery('.faucet2degree').html('');
   }    
}


function slabClick(sku){
    if (jQuery('.samplecheck').html() == 1){
        jQuery('.samplecheck').html('');
        return;
    }
    jQuery('.cpalert').remove();
    jQuery(".slab-info .small-slab-image").css("border","2px solid #fff");
    jQuery("#"+sku+' .small-slab-image').css("border","4px solid #f1ffbc");
    jQuery(".design-thickness").css("color","#000");
    jQuery(".design-thickness span").css("color","#000");
    var title = "<b>STONE: </b><span>"+jQuery("#"+sku+" .slabname").html()+"</span>";
    var realsku = jQuery("#"+sku).attr('realsku');
    jQuery(".skunum").html(realsku);
    jQuery(".design-stone").html(title);

         var scope = angular.element(document.getElementById("cp-controller")).scope();
        scope.$apply(function () {
           scope.setMaster('EDGING');
        });
}

// handle ajax calls and load data into Angular controller
function filterColors(){
    var baseurl =  jQuery(".baseurl").html();
    jQuery('.ajax-load').html('<center><img src="'+baseurl+'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center><br clear="left">');
    var thickness = jQuery( "select.color-thickness-options option:selected").val(); 
    jQuery('.design-thickness').html('<span style="color:#000">THICKNESS: </span>'+thickness+'cm');   
    var price = jQuery( "select.color-price-options option:selected").val();
    var color = jQuery( "select.color-color-options option:selected").val();
    var type = jQuery( "select.color-type-options option:selected").val();
    var veins = jQuery( "select.color-veins-options option:selected").val();
    var tones = jQuery( "select.color-tones-options option:selected").val();
    if (jQuery( ".color-remnants-options").is(':checked') === true){ 
        var remenants = 1;
    }else{
        var remenants = 0;
    }
    var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.filterSlabs(thickness,price,color,type,veins,tones,remenants);
    });
}

function addToCart(){
    var baseurl =  jQuery(".baseurl").html();
    jQuery(".next-button").remove();
    jQuery('.price').html('<center><img src="'+baseurl+'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center><br clear="left">');
    var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.setLoadCart();
    });
}

function quoteTab(){
    jQuery('.quoteme').css('background-color', '#808184');
    jQuery('.quoteform').css('display','block');
}

function nextTab(name){

    if (name == 'total'){
        jQuery(".next-tab").html('');
        var baseurl =  jQuery(".baseurl").html();
        jQuery('.faucets').html('<center><img src="'+baseurl+'skin/frontend/rwd/default/images/countertops/cp-spinner.gif"></center><br clear="left">');
        var scope = angular.element(document.getElementById("cp-controller")).scope();
        scope.$apply(function () {
        scope.setTotals();
        });
        return;
    }

        jQuery('.cpalert').remove();

        var section = jQuery(".toggle-tabs .current span").html();
        var checkstone = jQuery(".design-stone").html();
        var edgecheck = jQuery('.edgetype').html();
        var skip = 0;

        if (section === 'STONE'){
            if (checkstone.length == 0 ){
                skip = 0;
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
            }
        }else if (section === 'EDGING'){
            
            if (checkstone.length == 0 ){
                skip = 0;
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select a stone first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select a stone first. </span>');
            }else if (edgecheck.length == 0){
                skip = 0;
                jQuery('.cp-message p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
                jQuery('.cp-message2 p').prepend('<span class="cpalert">You need to select an edging type first. </span>');
            }
        }



    if (section == "DIMENSIONS"){
        jQuery(".back-button").css('display', 'block');
        section = "STONE";
    }else if (section == "STONE"){
        section = "EDGING";
    }else if (section == "EDGING"){
        section = "SINKS";
    }else if (section == "SINKS"){

   
        jQuery(".next-button").css('display', 'none');
        section = "FAUCETS";
    }else if (section == "FAUCETS"){
         section = "PRICE";
    }

    if (checkstone.length < 1 && section == "EDGING" ){

    }else{

         var scope = angular.element(document.getElementById("cp-controller")).scope();
        scope.$apply(function () {
           scope.setMaster(section);
        });

    }
}

function backTab(name){
    
    // get the section
    var section = jQuery(".toggle-tabs .current span").html();
    if (section == "STONE"){
        jQuery(".back-button").css('display', 'none');
        section = "DIMENSIONS";
    }else if (section == "EDGING"){
        section = "STONE";
    }else if (section == "SINKS"){
        section = "EDGING";
    }else if (section == "FAUCETS"){

            jQuery(".next-button").css('display', 'block');
            section = "SINKS";

    }else if (section == "PRICE"){
        var sinknum = jQuery('.sinknum').html();
        if (sinknum == '' || sinknum == '0' ){
            section = "SINKS";
        }else{
            section = "FAUCETS";
            
        }
    }



     var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.setMaster(section);
    });
}

function showColors(){
    var myClass = jQuery('#colorclick').attr("class");
   
   if (myClass == "downarrow"){
        jQuery('#colorclick').attr("class","uparrow");
        jQuery('.edge-search').show();
   }else{
        jQuery('#colorclick').attr("class","downarrow");
        jQuery('.edge-search').hide();
   }
}

function edgeover(number){
    jQuery('.edge-under'+number).hide();
    jQuery('.edge-over'+number).show();
}

function edgeout(number){
    var checkclass = jQuery('.edge-over'+number).attr("class");
    var check = checkclass.split(' ');
    if (check[1] != 'active' ){
        jQuery('.edge-under'+number).show();
        jQuery('.edge-over'+number).hide();
    }
}

function edgeselect(number,name,id){

    var clearclass = jQuery('.active').attr("class");
    jQuery('.edgechoice').html(number);
    if(typeof clearclass !== 'undefined'){
        var clear = clearclass.split(' ');
        var first = clear[0].replace('edge-over', 'edge-under');
        jQuery('.'+clear[0]).hide();
        jQuery('.'+first).show();
    }

    jQuery('.cp-counter-front .edging').html(name);
    jQuery('.cp-counter-back .edging').html(name);
    jQuery('.cp-counter-left .edging').html(name);
    jQuery('.cp-counter-right .edging').html(name);
    jQuery('.edgename').html(name);

     if (jQuery('.pedge-top-check').is(':checked') === true){ 
        jQuery('.cp-counter-back .edging').css('display','inline');
    }

    if (jQuery('.pedge-right-check').is(':checked') === true){ 
        jQuery('.cp-counter-right .edging').css('display','block');
    }

     if (jQuery('.pedge-left-check').is(':checked') === true){ 
        jQuery('.cp-counter-left .edging').css('display','block'); 
    }

    jQuery('.edgetype').html(id);

    jQuery('.edge-image img').removeClass('active');
    jQuery('.edge-image').removeClass('tactive');
    jQuery('.edge-under'+number).hide();
    jQuery('.edge-over'+number).show();
    jQuery('.edge-over'+number).addClass('active');
    jQuery('.ei'+number).addClass('tactive');
}

function choosePolish(loc){
    if (loc == 'top'){
        var topcolor = jQuery('.polish-back').css('background-color');


        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.polishback').html('');
            jQuery('.polish-back').css('background-color','#fff');
            jQuery('.polish-back .edge-text-top').css('color','#000');
            //check if backsplash is active
            if (jQuery('.bedge-top-check').is(':checked') === false){ 
                jQuery('.cp-counter-back').css('display','none');
            }
            jQuery('.cp-counter-back .polish').css('display','none');
            jQuery('.cp-counter-back .edging').css('display','none');
            jQuery('.pedge-top-check').prop('checked', false);
        }else{
            jQuery('.polishback').html('1');
            jQuery('.backsplashback').html('');
            jQuery('.pedge-top-check').prop('checked', true);
            jQuery('.polish-back').css('background-color','#199CA3'); 
            jQuery('.polish-back .edge-text-top').css('color','#F1FFBC');
            jQuery('.cp-counter-back').css('display','block');
            jQuery('.cp-counter-back .polish').css('display','inline');
            jQuery('.cp-counter-back .edging').css('display','inline');

            jQuery('.cp-counter-back').css('border-left','1px solid #000'); 
            jQuery('.cp-counter-back').css('border-top','1px solid #000'); 
            jQuery('.cp-counter-back').css('border-right','1px solid #000');

            jQuery('.cp-counter-back').css('background-color','');
            jQuery('.cp-counter-back .backsplash').css('display','none');
            jQuery(".bedge-top-check").attr("checked", false);
            jQuery('.backsplash-back').css('background-color','#fff');
            jQuery('.backsplash-back .backsplash-text-top').css('color','#000');            
        }
        
    }

   if (loc == 'left'){
        var topcolor = jQuery('.polish-left').css('background-color');

        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.polishleft').html('');
            jQuery('.polish-left').css('background-color','#fff');
            jQuery('.polish-left .edge-text-left').css('color','#000');

            if (jQuery('.bedge-left-check').is(':checked') === false){ 
                jQuery('.cp-counter-left').css('border-bottom','0px'); 
                jQuery('.cp-counter-left').css('border-top','0px'); 
                jQuery('.cp-counter-left').css('border-left','0px');
            }
            jQuery('.cp-counter-left .polish').css('display','none');
            jQuery('.cp-counter-left .edging').css('display','none');
            jQuery('.pedge-left-check').prop('checked', false);            
        }else{

            jQuery('.polishleft').html('1');
            jQuery('.backsplashleft').html('');
            jQuery('.pedge-left-check').prop('checked', true);
            jQuery('.polish-left').css('background-color','#199CA3'); 
            jQuery('.polish-left .edge-text-left').css('color','#F1FFBC');

            jQuery('.cp-counter-left').css('border-bottom','1px solid #000'); 
            jQuery('.cp-counter-left').css('border-top','1px solid #000'); 
            jQuery('.cp-counter-left').css('border-left','1px solid #000');
            jQuery('.cp-counter-left .polish').css('display','block');
            jQuery('.cp-counter-left .edging').css('display','block');                         

            jQuery('.cp-counter-left').css('background-color','');
            jQuery('.cp-counter-left .backsplash').css('display','none');
            jQuery(".bedge-left-check").attr("checked", false);
            jQuery('.backsplash-left').css('background-color','#fff');
            jQuery('.backsplash-left .backsplash-text-right').css('color','#000');
        }
        
    }

    if (loc == 'right'){
        var topcolor = jQuery('.polish-right').css('background-color');

        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.polishright').html('');
            jQuery('.polish-right').css('background-color','#fff');
            jQuery('.polish-right .edge-text-right').css('color','#000');
            jQuery('.cp-counter-right .polish').css('display','none');
            jQuery('.cp-counter-right .edging').css('display','none');
            jQuery('.pedge-right-check').prop('checked', false);
            //check if backsplash is active
            if (jQuery('.bedge-right-check').is(':checked') === false){ 
                jQuery('.cp-counter-right').css('display','none');
            }            
        }else{
            jQuery('.polishright').html('1');
            jQuery('.backsplashright').html('');
            jQuery('.pedge-right-check').prop('checked', true);
            jQuery('.polish-right').css('background-color','#199CA3'); 
            jQuery('.polish-right .edge-text-right').css('color','#F1FFBC');
            jQuery('.cp-counter-right').css('display','block');
            jQuery('.cp-counter-right .polish').css('display','block');
            jQuery('.cp-counter-right .edging').css('display','block');
            jQuery('.cp-counter-right').css('border-top','1px solid #000'); 
            jQuery('.cp-counter-right').css('border-right','1px solid #000'); 
            jQuery('.cp-counter-right').css('border-bottom','1px solid #000');

            jQuery('.cp-counter-right').css('background-color','');
            jQuery('.cp-counter-right .backsplash').css('display','none');
            jQuery(".bedge-right-check").attr("checked", false);
            jQuery('.backsplash-right').css('background-color','#fff');
            jQuery('.backsplash-right .backsplash-text-right').css('color','#000');

        }
        
    }
}

function chooseBacksplash(loc){

    // check if the backsplash is available
    jQuery('.cpalert').remove();
    
    if (loc == 'top'){
        var topcolor = jQuery('.backsplash-back').css('background-color');

        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.backsplashback').html('');
            jQuery('.backsplash-back').css('background-color','#fff');
            jQuery('.backsplash-back .edge-text-top').css('color','#000');

            //check if polish is active
            if (jQuery('.pedge-top-check').is(':checked') === false){ 
                jQuery('.cp-counter-back').css('display','none');              
            }else{
                jQuery('.cp-counter-back .polish').css('display','inline');
                jQuery('.cp-counter-back .edging').css('display','inline');                  
            }
            jQuery('.cp-counter-back').css('background-color','');

            jQuery('.cp-counter-back .backsplash').css('display','none');
            jQuery('.bedge-top-check').prop('checked', false);
            jQuery('.cp-message p .cpalert').remove();
        }else{
            jQuery('.backsplashback').html('1');
            jQuery('.polishback').html('');
            jQuery('.cp-counter-back').css('border','0px');
            jQuery('.bedge-top-check').prop('checked', true);
            jQuery('.backsplash-back').css('background-color','#199CA3'); 
            jQuery('.backsplash-back .edge-text-top').css('color','#F1FFBC');
            jQuery('.cp-counter-back').css('display','block');
            jQuery('.cp-counter-back').css('background-color','#F1FFBC');
            jQuery('.cp-counter-back .backsplash').css('display','inline');

            jQuery(".pedge-top-check").attr("checked", false);
            jQuery('.polish-back').css('background-color','#fff');
            jQuery('.edge-text-top').css('color','#000');

            jQuery('.cp-counter-back .polish').css('display','none');
            jQuery('.cp-counter-back .edging').css('display','none');
            // check if backsplash is too small
            var scope = angular.element(document.getElementById("cp-controller")).scope();
            scope.$apply(function () {
               scope.setBackCheck(2);
            }); 
        }
        

    }

   if (loc == 'left'){
        var topcolor = jQuery('.backsplash-left').css('background-color');

        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.backsplashleft').html('');
            jQuery('.backsplash-left').css('background-color','#fff');
            jQuery('.backsplash-left .edge-text-left').css('color','#000');

            if (jQuery('.pedge-left-check').is(':checked') === false){ 
                jQuery('.cp-counter-left').css('border-bottom','0px'); 
                jQuery('.cp-counter-left').css('border-top','0px'); 
                jQuery('.cp-counter-left').css('border-left','0px');                
            }else{
                jQuery('.cp-counter-left .polish').css('display','block');
                jQuery('.cp-counter-left .edging').css('display','block');                 
            }
            jQuery('.cp-counter-left').css('background-color','');
            jQuery('.cp-counter-left .backsplash').css('display','none'); 
            jQuery('.bedge-left-check').prop('checked', false);


        }else{
            //figureout height
            var leftheight = jQuery('.cp-counter-left').height();
            leftheight = leftheight-40;
            jQuery('.backsplashleft .backsplash').css('margin-top',leftheight);            
            jQuery('.backsplashleft').html('1');
            jQuery('.polishleft').html('');
            jQuery('.cp-counter-left').css('border','0px');
            jQuery('.bedge-left-check').prop('checked', true);
            jQuery('.backsplash-left').css('background-color','#199CA3'); 
            jQuery('.backsplash-left .edge-text-left').css('color','#F1FFBC');

            jQuery('.cp-counter-left').css('background-color','#F1FFBC');
            jQuery('.cp-counter-left .backsplash').css('display','block');

            jQuery(".pedge-left-check").attr("checked", false);
            jQuery('.polish-left').css('background-color','#fff');
            jQuery('.edge-text-left').css('color','#000');
            jQuery('.cp-counter-left .polish').css('display','none');
            jQuery('.cp-counter-left .edging').css('display','none');                        
            // check if backsplash is too small
            var scope = angular.element(document.getElementById("cp-controller")).scope();
            scope.$apply(function () {
               scope.setBackCheck(1);
            });
        }
        
    }

    if (loc == 'right'){
        var topcolor = jQuery('.backsplash-right').css('background-color');

        if (topcolor == 'rgb(25, 156, 163)'){
            jQuery('.backsplashright').html('');
            jQuery('.backsplash-right').css('background-color','#fff');
            jQuery('.backsplash-right .edge-text-right').css('color','#000');
            //check if polish is active
            if (jQuery('.pedge-right-check').is(':checked') === false){ 
                jQuery('.cp-counter-right').css('display','none');
            }else{
                jQuery('.cp-counter-right .polish').css('display','block');
                jQuery('.cp-counter-right .edging').css('display','block');
            }
            jQuery('.cp-counter-right').css('background-color','');
            jQuery('.cp-counter-right .backsplash').css('display','none');   
            jQuery('.bedge-right-check').prop('checked', false);
                      
        }else{
            jQuery('.backsplashright').html('1');
            jQuery('.polishright').html('');
            jQuery('.cp-counter-right').css('border','0px');
            jQuery('.bedge-right-check').prop('checked', true);
            jQuery('.backsplash-right').css('background-color','#199CA3'); 
            jQuery('.backsplash-right .edge-text-right').css('color','#F1FFBC');

            jQuery('.cp-counter-right').css('display','block');
            jQuery('.cp-counter-right').css('background-color','#F1FFBC');
            jQuery('.cp-counter-right .backsplash').css('display','block');
            jQuery(".pedge-right-check").attr("checked", false);
            jQuery('.polish-right').css('background-color','#fff');
            jQuery('.edge-text-right').css('color','#000');

            jQuery('.cp-counter-right .polish').css('display','none');
            jQuery('.cp-counter-right .edging').css('display','none');

            // check if backsplash is too small
            var scope = angular.element(document.getElementById("cp-controller")).scope();
            scope.$apply(function () {
               scope.setBackCheck(3);
            });

        }
        
    }
}

function edgeHoverShow(cot){
    jQuery(".edge-image .help"+cot).show();
}

function edgeHoverHide(cot){
    jQuery(".edge-image .help"+cot).hide();
}

function sinkHoverShow(cot){
    jQuery(".sinks-bowls .help"+cot).show();
}

function sinkHoverHide(cot){
    jQuery(".sinks-bowls .help"+cot).hide();
}

function chooseNum(size){
    jQuery('.sinknum').html(size);
// get the size of the sink so we can check if you can have 2
    var slabwidth = jQuery('.shrink-width-span').html();
    slabwidth = slabwidth.substring(0, slabwidth.length - 1); 
    //slabwidth = slabwidth[0];
    
    jQuery('input:radio[name=sinknum]').val([size]);

    var slabheight = jQuery('.shrink-height-span').html();
    slabheight = slabheight.substring(0, slabheight.length - 1); 
   // slabheight = slabheight[0];



  var sinktype =  jQuery('.sink-selected').attr('sinkname');
    if (typeof sinktype === "undefined") {
        sinktype = '';
    }

    // check if sink is selected
    if (jQuery('.sink-div').hasClass('sink-selected')){
        var nosinkselected = 1;
    }else{
        var nosinkselected = 0; 
    }


        if (size == 1){
            jQuery('.design-sinks').html('<b>SINKS:</b> <span>'+size+' '+sinktype+'</span>');
            jQuery('.1sinkok').css('display', 'block');
            jQuery('.2sinkok').css('display', 'none');
            jQuery('.sinks-bowls').css('display', 'block');
            jQuery('.sink2-display').css('display','none');
            jQuery('.sink2-display-dash-line').css('display','none');
            jQuery('.sink2-distance').css('display','none');
            jQuery('.sinks-bowl2').css('display','none');
            // get sink type
            var sku =  jQuery('.sink-selected').attr('sinksku');
            var size =  jQuery('.sink-selected').attr('sinksize');             
            chooseSink(sku, size, 'center');

        }

        if (size == 2){


                jQuery('.design-sinks').html('<b>SINKS:</b> <span>'+size+' '+sinktype+'</span>');                 
                jQuery('.2sinkok').css('display', 'block');
                jQuery('.1sinkok').css('display', 'none');
                jQuery('.sinks-bowls').css('display', 'block');
                if (nosinkselected == 1){
                    jQuery('.sinks-bowl1').css('display','block');
                    jQuery('.sinks-bowl2').css('display','block');
                }

                // get sink type
                var sku =  jQuery('.sink-selected').attr('sinksku');
                var size =  jQuery('.sink-selected').attr('sinksize');
                chooseSink(sku, size, 'center'); 

        }


    if (size == 0){
        jQuery('.sink1-display').css('display','none');
        jQuery('.sink1-display-dash-line').css('display','none');
        jQuery('.sink1-distance').css('display','none');         
        jQuery('.sink2-display').css('display','none');
        jQuery('.sink2-display-dash-line').css('display','none');
        jQuery('.sink2-distance').css('display','none'); 
        jQuery('.sinks-bowl1').css('display','none');
        jQuery('.sinks-bowl2').css('display','none');        
        jQuery('.sink-div').removeClass('sink-selected');
        jQuery('.sinks-bowls').css('display', 'none');
        
        jQuery('.design-sinks').html('');
        jQuery('.design-faucets').html('');
        jQuery('.faucettype').html('');
        jQuery('.sinksku').html('');
        jQuery('.sinktype').html('');
        jQuery('.sink1dis').html('');
        jQuery('.sink2dis').html('');
        jQuery('.sinksize').html('');
    }else{

        // get sink type
        var sku =  jQuery('.sink-selected').attr('sinksku');
        var size =  jQuery('.sink-selected').attr('sinksize');
        
        //chooseSink(sku, size);
    }

}

function restoreSink(sinknum){
    var sink1dis = jQuery('.sink1dis').html();
    var sink2dis = jQuery('.sink2dis').html();
    var sinksize = jQuery('.sinksize').html();
    
    var halfsink = sinksize/2;
    

    var sink1 = (sink1dis-halfsink)*8;
    var sink2 = (sink2dis-halfsink)*8;

    jQuery('.sink1-display').css('left',sink1+'px');
    jQuery('.sink2-display').css('left',sink2+'px');

}

function restoreVessel(){
    var sinknum = parseFloat(jQuery('.sinknum').html());
    var vessel1left = parseFloat(jQuery('.vessel1left').html());
    var vessel1top = parseFloat(jQuery('.vessel1top').html());
     var vesselsize1 = parseFloat(jQuery('.vesselsize1').html());
     var v1half = parseFloat(roundme(vesselsize1/2));



     vessel1left = vessel1left*8;
     vessel1top = (vessel1top-v1half)*8;
     
     vesselsize1 = vesselsize1*8;
     var vesselrad1 = vesselsize1/2;

     if (sinknum == 1){
        jQuery('input[name=sinknum]:eq(1)').click();
        jQuery('.sinks-vessel2').css('display','none'); 
     }
     vessel1left=vessel1left-vesselrad1;
     if (vesselsize1 > 1.75){
        vessel1left = vessel1left+((vesselsize1/2)-2);
     }

    jQuery('.sink1-display').css('display','block');
    jQuery('.sink1-display').addClass('vessel');
    jQuery('.sink1-display').css('left',vessel1left);

    jQuery('.sink1-display').css('top',vessel1top);
    jQuery('.sink1-display').css('width',vesselsize1);
    jQuery('.sink1-display').css('height',vesselsize1);
    jQuery('.sink1-display').css('border-radius',vesselrad1);
    jQuery('.sink1-display-dash-line').css('display', 'block');
    jQuery('.sink1-distance').css('display', 'block');
    jQuery('.sinks-vessel1').css('display', 'block');
    jQuery('.sinks-bowl1').css('display', 'none');
    jQuery('.sinks-bowl2').css('display', 'none');
    
    if (sinknum == 2){
        var vessel2left = parseFloat(jQuery('.vessel2left').html());
        var vessel2top = parseFloat(jQuery('.vessel2top').html());
        var vesselsize2 = parseFloat(jQuery('.vesselsize2').html());
        var v2half = parseFloat(roundme(vesselsize2/2));

        vessel2left = vessel2left*8;
        vessel2top = (vessel2top-v2half)*8;
        vesselsize2 = vesselsize2*8;
        var vesselrad2 = vesselsize2/2;
        vessel2left = vessel2left-vesselrad2;

        if (vesselsize2 > 1.75){
        vessel2left = vessel2left+((vesselsize2/2)-2);
        }        
       jQuery('.sink2-display').css('display','block'); 
       jQuery('.sink2-display').addClass('vessel'); 
        jQuery('.sink2-display').css('left',vessel2left);
        jQuery('.sink2-display').css('top',vessel2top);
        jQuery('.sink2-display').css('width',vesselsize2); 
        jQuery('.sink2-display').css('height',vesselsize2);
        jQuery('.sink2-display').css('border-radius',vesselrad2);
        jQuery('.sink2-display-dash-line').css('display', 'block');
        jQuery('.sink2-distance').css('display', 'block');            
    }


    
     vesselsize1 = jQuery('.vesselsize1').html();
     vesselsize2 = jQuery('.vesselsize2').html();

     if (vesselsize1 > 1.75){

        jQuery('.centerhole1').css('display','block');
     }

     if (vesselsize2 > 1.75){
        jQuery('.centerhole2').css('display','block');
     }



}

function chooseSink(sku, size, center){


    var sinksize =jQuery('.sink-image'+sku).attr('sinksize');

    var slabsize = jQuery('.shrink-width-span').html();
    slabsize = slabsize.substring(0, slabsize.length - 1);
    slabsize = parseFloat(slabsize);

    jQuery('.sinktype').html(sinksize);
    jQuery('.sink-div').removeClass('sink-selected');
    jQuery('.sink-image'+sku).addClass('sink-selected');

    var sinknum = jQuery('input[name=sinknum]:checked').val(); 
    var sinktype =  jQuery('.sink-selected').attr('sinkname');
    var sinkwidth =  jQuery('.sink-selected').attr('sinkwidth');
    
    jQuery('.sinksize').html(sinkwidth);

    jQuery('.sink1-display').css('height','');
    jQuery('.sink1-display').css('border-radius','');
    jQuery('.sink1-display').css('width','');
    jQuery('.sink2-display').css('height','');
    jQuery('.sink2-display').css('border-radius','');
    jQuery('.sink2-display').css('width','');

    if (typeof(size) != "undefined" && size != 'Vessel'){
        jQuery('.centerhole1').css('display','none');
        jQuery('.centerhole2').css('display','none');
    }
    jQuery('.sinks-vessel1').css('display','none');
    jQuery('.sinks-vessel2').css('display','none');
    jQuery('.sinks-bowl1').css('display','none');
    jQuery('.sinks-bowl2').css('display','none');
    jQuery('.sink1-display-dash-line').css('display','none'); 
    jQuery('.sink1-distance').css('display','none'); 


    if (sinknum < 1){
        jQuery('.sinknum').html(1);
        jQuery('input:radio[name=sinknum]').val(['1']);
         if (size == 'Vessel'){
            jQuery('.sinks-vessel1').css('display','block');
        }else{
            jQuery('.sinks-bowl1').css('display','block');
        }
        sinknum = 1;
    }


    if (sinknum == 1 ){
        if (size == 'Vessel'){
            jQuery('.sinks-vessel1').css('display','block');
            jQuery('.vessel1left').html();
        }else{
            jQuery('.sinks-bowl1').css('display','block');
        }
    }

    jQuery('.design-thickness').css('color','#000');
    jQuery('.design-stone').css('color','#000');
    jQuery('.design-stone b').css('color','#000');
    jQuery('.design-stone span').css('color','#000');


        

    var oldsku = jQuery('.sinksku').html();

    if (oldsku == sku && oldsku != '' || oldsku && typeof sku === 'undefined'){
        var oldsinkname = jQuery('.design-sinks span').html();
        jQuery('.design-sinks').html('<b>SINKS:</b> <span>'+oldsinkname+'</span>');
    }else{      
        jQuery('.sinksku').html(sku);
        jQuery('.design-sinks').html('<b>SINKS:</b> <span>'+sinknum+' '+sinktype+'</span>');
    }



    if (sinknum == 2){

        if (size == 'Vessel'){
            jQuery('.sinks-vessel1').css('display','block');
            jQuery('.sinks-vessel2').css('display','block');
        }else{
            jQuery('.sinks-bowl1').css('display','block');
            jQuery('.sinks-bowl2').css('display','block');
        }
    }

    jQuery('.sink1-display').css('display','none');
    jQuery('.sink1-display').removeClass('small-oval');
    jQuery('.sink1-display').removeClass('large-oval');
    jQuery('.sink1-display').removeClass('small-square');
    jQuery('.sink1-display').removeClass('large-square');
    jQuery('.sink1-display').removeClass('vessel');
    jQuery('.sink1-display').css('top','');
    jQuery('.sink1-display').css('left','');

    jQuery('.sink2-display').css('display','none');
    jQuery('.sink2-display').removeClass('small-oval');
    jQuery('.sink2-display').removeClass('large-oval');
    jQuery('.sink2-display').removeClass('small-square');
    jQuery('.sink2-display').removeClass('large-square'); 
    jQuery('.sink2-display').removeClass('vessel');   
    jQuery('.sink2-display-dash-line').css('display','none'); 
    jQuery('.sink2-distance').css('display','none');
    jQuery('.sink2-display').css('top',''); 
    jQuery('.sink2-display').css('left',''); 


    jQuery('.vesselnotify').remove();

    if (size == "Small Oval"){
            clearVessel();   
            jQuery('.sink1-display').addClass('small-oval');
            jQuery('.sink1-display').css('display','block');
            jQuery('.sink1-display-dash-line').css('display','block'); 
            jQuery('.sink1-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(1);
            }
        
        if (sinknum == 2){
            jQuery('.sink2-display').addClass('small-oval');
            jQuery('.sink2-display').css('display','block');
            jQuery('.sink2-display-dash-line').css('display','block'); 
            jQuery('.sink2-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(2);
            }
        }
    }

    if (size == "Large Oval"){

            clearVessel();
            jQuery('.sink1-display').addClass('large-oval');
           jQuery('.sink1-display').css('display','block');
           jQuery('.sink1-display-dash-line').css('display','block'); 
           jQuery('.sink1-distance').css('display','block'); 

           if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(1);
            }
        
        if (sinknum == 2){

            jQuery('.sink2-display').addClass('large-oval');
           jQuery('.sink2-display').css('display','block');
           jQuery('.sink2-display-dash-line').css('display','block'); 
           jQuery('.sink2-distance').css('display','block'); 

           if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(2);
            }
        }
    }  

    if (size == "Small Square"){
            clearVessel();
            jQuery('.sink1-display').addClass('small-square');
            jQuery('.sink1-display').css('display','block');
            jQuery('.sink1-display-dash-line').css('display','block'); 
            jQuery('.sink1-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(1);
            }
        
        if (sinknum == 2){
            jQuery('.sink2-display').addClass('small-square');
            jQuery('.sink2-display').css('display','block');
            jQuery('.sink2-display-dash-line').css('display','block'); 
            jQuery('.sink2-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(2);
            }
        }
    } 

    if (size == "Large Square"){
            clearVessel();
            jQuery('.sink1-display').addClass('large-square');
            jQuery('.sink1-display').css('display','block');
            jQuery('.sink1-display-dash-line').css('display','block'); 
            jQuery('.sink1-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(1);
            }

        if (sinknum == 2){
            jQuery('.sink2-display').addClass('large-square');
            jQuery('.sink2-display').css('display','block');
            jQuery('.sink2-display-dash-line').css('display','block'); 
            jQuery('.sink2-distance').css('display','block'); 
            if (center){
                moveSink1('center',size,sinknum);
            }else{
                restoreSink(2);
            }
        }
    }  

    if (size == "Vessel"){
        var vsku = jQuery('.sinksku').html();

        // restore
        if (vsku.length > 0){

        }
        jQuery('.cpalert').remove();
        jQuery('.cp-message').append('<b class="vesselnotify">. - Vessel sink not included.</b>');
        jQuery('.sink1-display').addClass('vessel');
        jQuery('.sink1-display').css('display','block');

        if (center){
            moveSink2('center',size,sinknum);
        }
        
        if (sinknum == 2){
            jQuery('.sink2-display').addClass('vessel');
            jQuery('.sink2-display').css('display','block');
            if (center){
                moveSink2('center',size,sinknum);
            }
        }

        // faucets
        jQuery('.sink1-middlehole').css('display','none');
        jQuery('.sink1-lefthole').css('display','none');
        jQuery('.sink1-righthole').css('display','none');
        jQuery('.sink2-middlehole').css('display','none');
        jQuery('.sink2-lefthole').css('display','none');
        jQuery('.sink2-righthole').css('display','none');
        jQuery('.faucettype').html('');
                
    }

    var faucettype = jQuery('.faucettype').html();
    if (faucettype.length > 0){
        holeClick(faucettype);
    }    

}

function clearVessel(){
            // clear vessel variables
        jQuery('.vesselsize1').html('');
        jQuery('.vesselsize2').html('');
        jQuery('.vessel1left').html('');
        jQuery('.vessel2left').html('');
        jQuery('.vessel1top').html('');
        jQuery('.vessel2top').html('');
        jQuery('.faucet1dis').html('');
        jQuery('.faucet1degree').html('');
        jQuery('.faucet2dis').html('');
        jQuery('.faucet2degree').html('');
}

function moveSink1(move,size,sinknum){

    jQuery('.cpalert').remove();
    if (typeof size === "undefined" || size == '') {
       var size = jQuery('.sink-selected').attr('sinksize');
    }

     var sinkcheck = '';

    if (typeof sinknum === "undefined" || sinknum == ''){
        var sinkcheck = jQuery('input[name=sinknum]:checked').val();
    }

    var sink2width = jQuery('.sink2-display').css('width');
    sink2width = sink2width.split('px');
    sink2width = Number(sink2width[0]);

    var size = size.toLowerCase();

    size = size.split(' ');
    var sinkshape = size[0]+'-'+size[1]; 

    var cpwidth = jQuery('.cp-counter').css('width');
    cpwidth = cpwidth.split('px');
    cpwidth = Number(cpwidth[0]);
    var oldcpwidth = cpwidth;

    var cpheight = jQuery('.cp-counter').css('height');
    cpheight = cpheight.split('px');
    cpheight = Number(cpheight[0]);

    // if we have two bowls divide slab and layout sinks
    if (sinknum == 2 || sinkcheck == 2){
        cpwidth = cpwidth/2;
    }

    var totalinches = jQuery('.shrink-width-span').html();
    totalinches = totalinches.substring(0, totalinches.length - 1); 
    //totalinches =  Number(totalinches[0]);

    var oldtotalinches = totalinches;

    if (sinknum == 2 || sinkcheck == 2){
        totalinches = totalinches/2;
    }     

    var screenbowl1width = jQuery('.'+sinkshape).css('width');
    screenbowl1width = screenbowl1width.split('px');
    screenbowl1width = Number(screenbowl1width[0]);

    var bowlwidth = jQuery('.sink-selected').attr('sinkwidth');
    bowlwidth = roundme(bowlwidth);
    var leftdistance = Number(jQuery('.bowl1-distance').val());
 
    var rightdistance = Number(jQuery('.bowl2-distance').val());
    var oldright = rightdistance;

    // correct the size of sinks
    var sinktruesize = bowlwidth*8;
    jQuery('.sink1-display').width(sinktruesize);
    jQuery('.sink2-display').width(sinktruesize);


    // make sure user can't enter crazy large numbers
    if (move == 1 && sinkcheck == 2){
            var sink2inches = Number(jQuery('input[name=sinktwoedge]').val());
            var halfasink = roundme(((sink2width/8)/2));
            var limit = ((sink2inches-halfasink)-8)-halfasink;

            if (leftdistance > limit){
                leftdistance = limit;
            }
    }

    // make sure user can't enter crazy large numbers
    if (move == 2){
            var sink2inches = Number(jQuery('input[name=sinktwoedge]').val());
            var halfasink = roundme(((sink2width/8)/2));
            var limit = ((sink1inches+halfasink)+8)+halfasink;;

            if (rightdistance < limit){
                rightdistance = limit;
            }
    }


    if (move == 'minus'){
        if (sinknum == 2){
            rightdistance = rightdistance-0.25;
            bowlwidth = Number(bowlwidth);
            var limit = (leftdistance+bowlwidth)+8;
            
            if (rightdistance < limit){
                rightdistance = limit;
                jQuery('.cp-message').append('<span class="cpalert"> Distance between sinks must exceed 8"</span>');
            }else{
                jQuery('.bowl2-distance').val(rightdistance);
            }
            
            
        }else{
            leftdistance = leftdistance-0.25;
            bowlwidth = Number(bowlwidth);
            var limit = (bowlwidth/2)+4;
            if (leftdistance < limit){
                leftdistance = limit;
                jQuery('.cp-message').append('<span class="cpalert"> The edge of the sink needs to be 4" from any edge.</span>');
            }else{
                jQuery('.bowl1-distance').val(leftdistance);  
            }
        }
    }

    if (move == 'add'){
        if (sinknum == 2){
            rightdistance = rightdistance+0.25;

                if (rightdistance > (oldtotalinches-4)-(bowlwidth/2)){
                    rightdistance = (oldtotalinches-4)-(bowlwidth/2);
                    jQuery('.cp-message').append('<span class="cpalert"> The edge of the sink needs to be 4" from any edge.</span>');
                }else{
                    jQuery('.bowl2-distance').val(rightdistance);
                }
              

           
        }else{
            leftdistance = leftdistance+0.25; 
            var sink2inches = Number(jQuery('input[name=sinktwoedge]').val());
            var halfasink = roundme(((sink2width/8)/2));
            if (sinkcheck == 2){
                var limit = ((sink2inches-halfasink)-8)-halfasink;
            }else{
                var limit = ((sink2inches-halfasink)-4)-halfasink;
            }
            
           if (sinkcheck == 1 && leftdistance > (totalinches-4)-(bowlwidth/2) ){
                leftdistance = (totalinches-4)-(bowlwidth/2);
                jQuery('.cp-message').append('<span class="cpalert"> The edge of the sink needs to be 4" from any edge.</span>');

            }else if (sinkcheck == 2 && leftdistance > limit){

                leftdistance = limit;
                 jQuery('.cp-message').append('<span class="cpalert"> Distance between sinks must exceed 8"</span>');
            }
            //console.log('leftdistance:'+leftdistance+' totalinches:'+totalinches+' bowlwidth:'+bowlwidth);
            jQuery('.bowl1-distance').val(leftdistance); 
        }
    }

    if (move == 'center'){
    
        leftdistance = Number(totalinches/2);

        jQuery('.bowl1-distance').val(leftdistance);

        if (sinknum == 2){
            rightdistance = Number(totalinches/2)+totalinches;
            jQuery('.bowl2-distance').val(rightdistance);
        }
    }

    // ******************
    // New measurements
    //var bowl_1_distance = (((oldtotalinches/2)-4)*8);

    //console.log('bowl:'+bowl_1_distance+' oldtotalinches:'+oldtotalinches+' bowlwidth:'+bowlwidth);


    // round to nearest .25
    leftdistance = (Math.round(leftdistance * 4) / 4).toFixed(2);
    rightdistance = (Math.round(rightdistance * 4) / 4).toFixed(2);
    var checkzero = leftdistance.split('.');

    if (checkzero[1] == 00){
        leftdistance = Number(checkzero[0]);
    }

    if (sinknum == 2){
        var checkzero2 = rightdistance.split('.');
        if (checkzero2[1] == 00){
            rightdistance = Number(checkzero2[0]);
        }
    }

    // Inch Limits
    var inchlimit = totalinches-8; // minus 4" on either side
    var widthlimit = cpwidth-20;
    if (sinknum == 2 || sinkcheck == 2){
        widthlimit = cpwidth-10;
    }
    var oldwidthlimit = widthlimit;  // remove equivelent 4" on either side
    widthlimit = widthlimit-screenbowl1width; // remove the width of the bowl so we don't run over the right edge
    var increments = Math.round(widthlimit/inchlimit);
    
    if (move == 'add' & sinknum == 2 || move == 'minus' & sinknum == 2 || move == '2'){
        increments = Math.round((widthlimit+(widthlimit/2))/inchlimit);
    }

    //console.log('widthlimit:'+widthlimit+'=='+inchlimit+' screenbowl1width:'+screenbowl1width);

    var pixelchange = (leftdistance*8)-((bowlwidth/2)*8);
    var pixelchange2 = (rightdistance*8)-((bowlwidth/2)*8);     

    //console.log('pixelchange:'+pixelchange+' leftdistance:'+leftdistance);
    //console.log('inchlimit:'+inchlimit+' widthlimit:'+widthlimit+'==bowlwidth:'+bowlwidth+'==increments:'+increments+'==leftdistance:'+leftdistance+'==rightdistance:'+rightdistance+'==pixelchange2:'+pixelchange2);

    var rightlimit = (cpwidth-screenbowl1width)-10;


    if (sinknum == 2 && move == 'center'){
        pixelchange2 = (cpwidth+(cpwidth/2))-(sink2width/2);
        //console.log('cpwidth:'+cpwidth+'==pixelchange2:'+pixelchange2+" sink2width:"+sink2width);
    }
    
    //console.log('pixel2:'+pixelchange2);


    if (leftdistance < (bowlwidth/2)+4){
        leftdistance = (bowlwidth/2)+4;
    }

    //console.log('totalinches:'+totalinches+' leftdis:'+leftdistance+' cpwidth:'+cpwidth+' -screenbowl1width:'+screenbowl1width);

    if (sinkcheck == 1 && leftdistance > (totalinches-4)-(bowlwidth/2) ){
        leftdistance = (totalinches-4)-(bowlwidth/2);
    }

    // 4" = 32px
    if (pixelchange < 32){
        pixelchange = 32;
    }

    if (sinkcheck == 1 && pixelchange > (oldcpwidth-(bowlwidth*8))-32 ){
        pixelchange = (oldcpwidth-(bowlwidth*8))-32;
    }
    

    var linechange = pixelchange+((bowlwidth*8)/2);
    var dischange = linechange+5;


    jQuery('.sink1dis').html(leftdistance);
    if (sinknum == 2){ 
        jQuery('.sink2dis').html(rightdistance);
    }
    //console.log('left2:'+leftdistance+' ==increments:'+increments+' ==pixelchange:'+pixelchange);



    if (move == 'add' && sinknum != 2 || move == 'minus' && sinknum != 2 || move == 'center' || move == 1){

        jQuery('.sink1-display').css('left',pixelchange+'px');
        jQuery('.sink1-display-dash-line').css('width',linechange+'px');
        jQuery('.sink1-distance').css('left',dischange+'px');
        jQuery('.sink1-distance').html(leftdistance+'"');
    }

    if (sinknum == 2 || move == 2){
        linechange = pixelchange2+(screenbowl1width/2)+2;
        jQuery('.sink2-display').css('left',pixelchange2+'px');
        jQuery('.sink2-display-dash-line').css('width',linechange+'px');
        jQuery('.sink2-distance').css('left',linechange+3+'px');
        jQuery('.sink2-distance').html(rightdistance+'"');        
    }

    if (move == "center"){
        var sinkheight = jQuery('.sink1-display').height();
        var sinkcenter = (cpheight/2)-(sinkheight/2);
        var sinkbottomcenter = (cpheight/2)+(sinkheight/2);
         var sinkbottomcenter2 = sinkbottomcenter+4;
        jQuery('.sink1-display').css('top',sinkcenter+'px');
        jQuery('.sink1-distance').css('top',sinkbottomcenter+'px');
        jQuery('.sink1-display-dash-line').css('top',sinkbottomcenter2+'px');

        jQuery('.sink1-distance').html(leftdistance+'"');
        jQuery('.sink1center').html(leftdistance+'"');
        if (sinknum == 2){
            jQuery('.sink2-display').css('top',sinkcenter+'px');
            jQuery('.sink2-distance').css('top',sinkbottomcenter+'px');
            jQuery('.sink2-display-dash-line').css('top',sinkbottomcenter2+'px');            
            jQuery('.sink2center').html(rightdistance+'"');
        }
    }

}

// move vessel sinks
function moveSink2(move,size,sinknum){

    if (typeof size === "undefined" || size == '') {
       var size = jQuery('.sink-selected').attr('sinksize');
    }


       
    var sinkcheck = jQuery('input[name=sinknum]:checked').val();
    

    var hole = Number(jQuery('.bowl1-distance-width').val());
    var hole2 = Number(jQuery('.bowl2-distance-width').val());

    var sink1width = jQuery('.sink1-display').css('width');
    sink1width = sink1width.split('px');
    sink1width = Number(sink1width[0]);

    var sink1top = jQuery('.sink1-display').css('top');
    sink1top = sink1top.split('px');
    sink1top = Number(sink1top[0]);

    var sink2width = jQuery('.sink2-display').css('width');
    sink2width = sink2width.split('px');
    sink2width = Number(sink2width[0]);

    var sink2top = jQuery('.sink2-display').css('top');
    sink2top = sink2top.split('px');
    sink2top = Number(sink2top[0]);


    var size = size.toLowerCase();
    size = size.split(' ');
    var sinkshape = size[0]+'-'+size[1]; 

    var cpwidth = jQuery('.cp-counter').css('width');
    cpwidth = cpwidth.split('px');
    cpwidth = Number(cpwidth[0]);
    var oldcpwidth = cpwidth;


    var cpheight = jQuery('.cp-counter').css('height');
    cpheight = cpheight.split('px');
    cpheight = Number(cpheight[0]);    

    // if we have two bowls divide slab and layout sinks
    if (sinknum == 2 || sinkcheck == 2){
        cpwidth = cpwidth/2;
    }

    var widthinches = jQuery('.shrink-width-span').html();
    widthinches = widthinches.substring(0, widthinches.length - 1); 
    //widthinches =  Number(widthinches[0]);

    var heightinches = jQuery('.shrink-height-span').html();
    heightinches = heightinches.substring(0, heightinches.length - 1); 
    //heightinches =  Number(heightinches[0]);


    var vesselsize1 = Number(jQuery('.vesselsize1').html());
    var vesselsize1half = parseFloat(roundme(vesselsize1/2));
    var vesselsize2 = Number(jQuery('.vesselsize2').html());
    var vesselsize2half = parseFloat(roundme(vesselsize2/2));
    var bowlwidth = jQuery('.sink-selected').attr('sinkwidth');
    var leftdistance = Number(jQuery('.vessel1-distance').val());
    var topdistance = Number(jQuery('.bowl1-distance-top').val());
    var oldtopdistance = jQuery('.bowl1-distance-top').val();

    var leftdistance2 = Number(jQuery('.vessel2-distance').val());
    var topdistance2 = Number(jQuery('.bowl2-distance-top').val());
    var oldtopdistance2 = jQuery('.bowl2-distance-top').val();



    var toplimit = 7;
    var bottomlimit = heightinches-7;
       

   if (move == 'left'){
        if (sinknum == 2){
            leftdistance2 = leftdistance2-0.25;
            var minlimit = (widthinches/2)+3.5;
            
             if (leftdistance2 <= minlimit){ 
                leftdistance2 = minlimit;
             }
             
            jQuery('.vessel2-distance').val(leftdistance2);
            jQuery('.sink2-distance').html(leftdistance2+'"');
            var move2vesselleft = leftdistance2*8;
        }else{       
            leftdistance = leftdistance-0.25;

             if (leftdistance <= 7){ 
                leftdistance = 7;
             }
            jQuery('.vessel1-distance').val(leftdistance);
            jQuery('.sink1-distance').html(leftdistance+'"');
            var move1vesselleft = leftdistance*8;              
        }
    }

    if (move == 'right'){

        if (sinknum == 2){

            leftdistance2 = leftdistance2+0.25; 
             

            if (leftdistance2 >= widthinches-7){ 
                leftdistance2 = widthinches-7;
             }
             jQuery('.vessel2-distance').val(leftdistance2);
             jQuery('.sink2-distance').html(leftdistance2+'"');
            var move2vesselleft = leftdistance2*8;
        }else{
            leftdistance = leftdistance+0.25; 
                             
            if (sinkcheck == 2){
                var widthlimit = (widthinches/2)-3.5;
            }else{
                var widthlimit = widthinches-7;
            }

            //console.log(leftdistance+' width:'+widthlimit+' widthinches:'+widthinches+' widthlimit:'+widthlimit);
            if (leftdistance >= widthlimit){ 
                leftdistance = widthlimit;
             }
             jQuery('.vessel1-distance').val(leftdistance);
             jQuery('.sink1-distance').html(leftdistance+'"');
            var move1vesselleft = leftdistance*8;
        } 


    }

   if (move == 'up'){
        
        if (sinknum  == 2){
             var checklimit =  7+vesselsize2half;
             
            topdistance2 = topdistance2-0.25;
            var realdis2 = (sink2top/8)-0.25;
            
            if (topdistance2 < checklimit){
                topdistance2 = checklimit;
                return;
            }
             jQuery('.bowl2-distance-top').val(topdistance2); 

             var move2vessel = realdis2*8; 

             if (move2vessel <= 10){
                move2vessel = 10;
             }

        }else{
            
            var checklimit =  7+vesselsize1half;
            topdistance = topdistance-0.25;
            var realdis = (sink1top/8)-0.25;
            
            if (topdistance < checklimit){
                topdistance = checklimit;
                return;
            }
             jQuery('.bowl1-distance-top').val(topdistance); 

             var move1vessel = realdis*8; 

             if (move1vessel <= 10){
                move1vessel = 10;
             }
        }  
            
    }

    if (move == 'down'){
 
        if (sinknum == 2){
            var checklimit  = (heightinches-7)-vesselsize2half;  
            var topdistance2 = topdistance2+0.25; 
            var realdis2 = (sink2top/8)+0.25;
            var topmaxmax = heightinches-7; 
            if (topdistance2 > checklimit){
                topdistance2 = checklimit;
                return;
            }
              jQuery('.bowl2-distance-top').val(topdistance2);   
            

            var move2vessel = realdis2*8;

        }else{
            var checklimit  = (heightinches-7)-vesselsize1half;   
            var topdistance = topdistance+0.25;
            var realdis = (sink1top/8)+0.25;
            
            if (topdistance > checklimit){
                topdistance = checklimit;
                return;
            }
            jQuery('.bowl1-distance-top').val(topdistance);           
             
            var move1vessel = realdis*8; 

        }
            
    }



    if (move == 'center'){

        var hole = (1.75/2);
        var whole = 1.75*8;
        hole = roundme(hole);
        sink1width = sink1width/2;
        //heightinches = roundme(heightinches/2-hole);

        var topheight = roundme((heightinches/2)-hole);
        //heightinches = roundme(topheight-(hole/2));
        heightinches = roundme(heightinches/2);
        

        if (sinknum == 2){
            var widthinches2 = roundme(((widthinches/4)*3)-hole);
            var widthcenter2 = roundme(((widthinches/4)*3)-hole); 
            var widthcenter = roundme((widthinches/4)-hole); 
            widthinches = roundme((widthinches/4)-hole);
           
            
            var heightinches2 = heightinches;            
        }else{
            widthinches = roundme(widthinches/2-hole);
            var widthcenter = widthinches;


        }

        leftdistance = widthinches*8;
        topdistance = topheight*8;
        var leftdistance2 = widthinches2*8;
        var topdistance2 = topheight*8; 
        heightinches = roundme(heightinches);
        heightinches2 = roundme(heightinches2);     
        
        jQuery('.sink1-display').css('left',leftdistance+'px');
        jQuery('.sink1-display').css('top',topdistance+'px');
        jQuery('.vessel1-distance').val(widthinches);
        jQuery('.bowl1-distance-top').val(heightinches);
        jQuery('.bowl1-distance-width').val(1.75);
        jQuery('.vessel1center').html(widthinches);
        jQuery('.sink1-display-dash-line').css('display','block');
        jQuery('.sink1-display-dash-line').css('width',leftdistance+sink1width+'px');
        jQuery('.sink1-display-dash-line').css('top',topdistance+whole+'px');
        jQuery('.sink1-distance').css('display','block');
        jQuery('.sink1-distance').html(widthcenter+'"');
        jQuery('.vessel1-distance').val(widthcenter);
        jQuery('.sink1-distance').css('left',leftdistance+sink1width+5+'px');
        jQuery('.sink1-distance').css('top',topdistance+whole-5+'px');

        if (sinknum == 2){
            jQuery('.sink2-display').css('left',leftdistance2+'px');
            jQuery('.sink2-display').css('top',topdistance2+'px');
            jQuery('.vessel2-distance').val(widthinches2);
            jQuery('.bowl2-distance-top').val(heightinches2);
            jQuery('.bowl2-distance-width').val(1.75); 
            jQuery('.vessel2center').html(widthinches2);
            jQuery('.sink2-display-dash-line').css('display','block');
            jQuery('.sink2-display-dash-line').css('width',leftdistance2+(sink2width/2)+'px');
            jQuery('.sink2-display-dash-line').css('top',topdistance2+whole+'px');
            jQuery('.sink2-distance').css('display','block');
            jQuery('.sink2-distance').html(widthcenter2+'"');
            jQuery('.vessel2-distance').val(widthcenter2);
            jQuery('.sink2-distance').css('left',leftdistance2+sink1width+5+'px');
            jQuery('.sink2-distance').css('top',topdistance2+whole-5+'px');                     
        }

    }else{

        jQuery('.sink1-display').css('left',move1vesselleft+'px');
        jQuery('.sink1-display').css('top',move1vessel+'px');

        jQuery('.sink1-display-dash-line').css('display','block');
        jQuery('.sink1-display-dash-line').css('width',move1vesselleft+(sink1width/2)+'px');
        jQuery('.sink1-display-dash-line').css('top',move1vessel+sink1width+'px');
        jQuery('.sink1-distance').css('display','block');
        jQuery('.sink1-distance').css('left',move1vesselleft+(sink1width/2)+3+'px');
        jQuery('.sink1-distance').css('top',move1vessel+sink1width-5+'px');


        if (sinknum == 2){
            if (move == 'left' || move == 'right' ){
                jQuery('.sink2-display').css('left',move2vesselleft+'px');

                jQuery('.sink2-display-dash-line').css('display','block');
                jQuery('.sink2-display-dash-line').css('width',move2vesselleft+(sink2width/2)+'px');
                jQuery('.sink2-distance').css('display','block');
                jQuery('.sink2-distance').css('left',move2vesselleft+(sink2width/2)+3+'px');
                
            }else{
                jQuery('.sink2-display').css('top',move2vessel+'px');
                jQuery('.sink2-display-dash-line').css('top',move2vessel+sink2width+'px');
                jQuery('.sink2-distance').css('top',move2vessel+sink2width-5+'px');
            }
        }
    }


    // variables for vessels
    var vessel1left = jQuery('.vessel1-distance').val();
    var vessel1top = jQuery('.bowl1-distance-top').val();
    var vesselsize1 = jQuery('.bowl1-distance-width').val();
    var vessel2left = jQuery('.vessel2-distance').val();
    var vessel2top = jQuery('.bowl2-distance-top').val();
    var vesselsize2 = jQuery('.bowl2-distance-width').val();

    jQuery('.vessel1left').html(vessel1left);
    jQuery('.vessel1top').html(vessel1top);
    jQuery('.vesselsize1').html(vesselsize1);

    if (sinknum == 2){
        jQuery('.vessel2left').html(vessel2left);
        jQuery('.vessel2top').html(vessel2top);
        jQuery('.vesselsize2').html(vesselsize2);
    }



}

// change size of hole diameter
function changeSize(move,sinknum){
    var sinktext = '';
    var sinkcheck = jQuery('input[name=sinknum]:checked').val();

    var holesize1 = jQuery('.bowl1-distance-width').val();
        holesize1 = holesize1/1;

    var holesize2 = jQuery('.bowl2-distance-width').val();
        holesize2 = holesize2/1;

    var hole1 = jQuery('.sink1-display').css('width');
        hole1 = hole1.split("px");
        hole1 = Number(hole1[0]);

    var hole2 = jQuery('.sink2-display').css('width');
        hole2 = hole2.split("px");
        hole2 = Number(hole2[0]);

    var cpheight = jQuery('.cp-counter').css('height');
    cpheight = cpheight.split('px');
    cpheight = Number(cpheight[0]); 

    var cpwidth = jQuery('.cp-counter').css('width');
    cpwidth = cpwidth.split('px');
    cpwidth = Number(cpwidth[0]);

    var widthinches = jQuery('.shrink-width-span').html();
    widthinches = roundme(widthinches.substring(0, widthinches.length - 1)); 
    //widthinches =  Number(widthinches[0]);

    var heightinches = jQuery('.shrink-height-span').html();
    heightinches = roundme(heightinches.substring(0, heightinches.length - 1)); 
    //heightinches =  Number(heightinches[0])

    if (move == 'small'){
        holesize1 = holesize1-0.25;
        holesize2 = holesize2-0.25;
    }

    if (move == 'large'){
        holesize1 = holesize1+0.25;
        holesize2 = holesize2+0.25;
    }

    console.log(holesize1);

    var maxsize = ((heightinches/2)-7)*2;
    if (maxsize > 10){
        maxsize = 10;
    }

    if (move == 13){
        if (holesize1 > maxsize){
            holesize1 = maxsize;
        }
        if (holesize1 < 1.75){
            holesize1 = 1.75;
        }
    }

    if (move == 23){
        if (holesize2 > maxsize){
            holesize2 = maxsize;
        }
        if (holesize2 < 1.75){
            holesize2 = 1.75;
        }
    }

    

    if (sinkcheck == 2){
        
        if (sinknum == 1){
            jQuery('.centerhole1').css('display','block');
            hole1 =  holesize1*8;
            var toppos1 = ((heightinches/2)-(holesize1/2))*8;
            var leftpos1 = ((widthinches/4)-(holesize1/2))*8;
        }else{
             jQuery('.centerhole2').css('display','block');
            hole2 =  holesize2*8;
            var toppos2 = ((heightinches/2)-(holesize2/2))*8;
            var leftpos2 = (((widthinches/2)+(widthinches/4))-(holesize2/2))*8;
        }

    }else{
        hole1 =  holesize1*8;
        jQuery('.centerhole1').css('display','block');
        var toppos1 = (cpheight/2)-(hole1/2);
        var leftpos1 = (cpwidth/2)-(hole1/2);
    }



    if (holesize1 >= 1.75 && holesize1 <= maxsize && sinknum == 1){
        jQuery('.cpalert').remove(); 
         var centersize = (heightinches/2)-(holesize1/2);
         centersize = roundme(centersize);

         var leftsize =  jQuery('.sink1-display').css('left');
         leftsize = leftsize.split('px');
         leftsize = roundme(leftsize[0]/8);

         //jQuery('.vessel1-distance').val(leftsize);
        // jQuery('.vessel1left').html(leftsize);

        // jQuery('.bowl1-distance-top').val(centersize);
        // jQuery('.vessel1top').html(centersize);

        jQuery('.bowl1-distance-width').val(holesize1);
        jQuery('.sink1-display').css('width',hole1+'px');
        jQuery('.sink1-display').css('height',hole1+'px');
        hole1 = hole1/2;
        jQuery('.sink1-display').css('border-radius',hole1+'px');
        jQuery('.sink1-display').css('top',toppos1+'px');

        jQuery('.sink1-display').css('left',leftpos1+'px');
        jQuery('.vesselsize1').html(holesize1);

        jQuery('.centerhole1').css('display','block');
        jQuery('.centerhole1').css('border','2px solid #a6a8ab');

        jQuery('.sink1-display-dash-line').css('width',leftpos1+hole1+'px');
        jQuery('.sink1-display-dash-line').css('top',toppos1+(hole1*2)+'px');
        jQuery('.sink1-distance').css('left',leftpos1+hole1+5+'px');
        jQuery('.sink1-distance').css('top',toppos1+(hole1*2)-5+'px');   

    }else if (holesize1 >= maxsize){
         jQuery('.cpalert').remove();
        jQuery('.cp-message').prepend('<span class="cpalert">This is the largest diameter possible for your dimensions. </span>');
    }

    if (holesize1 == 1.75){
        jQuery('.centerhole1').css('border','0px');
    }

    if (holesize2 >= 1.75 && holesize2 <= maxsize && sinknum == 2){
        jQuery('.cpalert').remove();
         var centersize = (heightinches/2)-(holesize2/2);
         centersize = roundme(centersize);

         var left2size =  jQuery('.sink2-display').css('left');
         var left2size2 = left2size.split('px');
         left2size2 = roundme(left2size2[0]/8);

         //jQuery('.vessel2-distance').val(left2size2);
         //jQuery('.vessel2left').html(left2size2);

        // jQuery('.bowl2-distance-top').val(centersize);
        // jQuery('.vessel2top').html(centersize);

        jQuery('.bowl2-distance-width').val(holesize2);
        jQuery('.sink2-display').css('width',hole2+'px');
        jQuery('.sink2-display').css('height',hole2+'px');
        hole2 = hole2/2;
        jQuery('.sink2-display').css('border-radius',hole2+'px');
        jQuery('.sink2-display').css('top',toppos2+'px');
        jQuery('.sink2-display').css('left',leftpos2+'px');
        jQuery('.vesselsize2').html(holesize2);

        jQuery('.centerhole2').css('display','block');
        jQuery('.centerhole2').css('border','2px solid #a6a8ab');

        jQuery('.sink2-display-dash-line').css('width',leftpos2+hole2+'px');
        jQuery('.sink2-display-dash-line').css('top',toppos2+(hole2*2)+'px');
        jQuery('.sink2-distance').css('left',leftpos2+hole2+5+'px');
        jQuery('.sink2-distance').css('top',toppos2+(hole2*2)-5+'px');          
    }else if (holesize2 >= maxsize){
        jQuery('.cpalert').remove();
        jQuery('.cp-message').prepend('<span class="cpalert">This is the largest diameter possible for your dimensions. </span>');
    }


    if (holesize1 > 1.75 || holesize2 > 1.75){
        sinktext = sinknum+' Pre-cut Hole for Vessel Sink 1.75"- 10" Diameter';
        jQuery('.design-sinks span').html(sinktext);
    }else{
        sinktext = sinknum+' Pre-cut Hole for Vessel Sink 1.75';
        jQuery('.design-sinks span').html(sinktext);
    }

    
    if (holesize2 == 1.75){
        jQuery('.centerhole2').css('border','0px');
    }
}

function moveFaucet(move){
    jQuery('.cpalert').remove();
    var slabwidth = jQuery('.cp-counter').css('width');
    slabwidth = removepx(slabwidth);
    var slabheight = jQuery('.cp-counter').css('height');
    slabheight = removepx(slabheight);
    var holeheight = jQuery('.sink1-display').css('height');
    holeheight = removepx(holeheight);
    var holetop = jQuery('.sink1-display').css('top');
    holetop = removepx(holetop);    
    var halfslab = slabwidth/2;
    var slabmax = slabheight-20;
    var slabmin = ((holeheight/2)/8)+1;
    if (slabmin < 4.5){
        slabmin = 4.5;
    } 

    var faucet1 = Number(jQuery('.faucet1-distance').val());

    if (!jQuery('.degree1-left').hasClass('degreehover')){
        var line1height = jQuery('.left45 .line1').css('height');
        line1height = removepx(line1height);
        var line1width = jQuery('.left45 .line1').css('width');
        line1width = removepx(line1width);
        var left45top = jQuery('.left45').css('top');
        left45top = removepx(left45top);
        var left45left = jQuery('.left45').css('left');
        left45left = removepx(left45left);

        // figure out distance to the top
        var sinkholetop = jQuery('.sinkholeleft1').css('top');
        sinkholetop = removepx(sinkholetop);
        var maxlimit = (holetop+(holeheight/2))-(line1height-sinkholetop);

        
        

        if (move == 'add' && maxlimit > 20){
            line1height = line1height+2;
            line1width = line1width+2;

            halfslab = line1width-7;
            var halftop = line1height+2;
            faucet1 = faucet1+.25;

                jQuery('.faucet1-distance').val(faucet1);
                jQuery('.lineinfo1').html(faucet1+'"');
                jQuery('.left45 .line1').css('height', line1height+'px');
                jQuery('.left45 .line1').css('width', line1width+'px');
                jQuery('.left45').css('top', '-'+halftop+'px');
                jQuery('.left45').css('left', '-'+halfslab+'px');
                        
            
        }else if (move == 'add' && maxlimit <= 20){
            jQuery('.cp-message p').prepend('<span class="cpalert">This is the furthest distance possible for your dimensions. </span>');
        }

        if (move == 'minus'){
            line1height = line1height-2;
            line1width = line1width-2;

            
            halfslab = line1width-7;
            var halftop = line1height+2;
            faucet1 = faucet1-.25;

            console.log('faucet1:'+faucet1+' slabmin:'+slabmin);
            if (faucet1 >= slabmin){
                jQuery('.faucet1-distance').val(faucet1);
                jQuery('.lineinfo1').html(faucet1+'"');
                jQuery('.left45 .line1').css('height', line1height+'px');
                jQuery('.left45 .line1').css('width', line1width+'px');
                jQuery('.left45').css('top', '-'+halftop+'px');
                jQuery('.left45').css('left', '-'+halfslab+'px');
            }
        }

    }

    if (!jQuery('.degree1-center').hasClass('degreehover')){
       var height = jQuery('.center1').css('height');
       height = removepx(height);
       var sinktop = jQuery('.sink1-display').css('top');
       sinktop = removepx(sinktop);
       sinktop = Number(sinktop);
        var sinkheight = jQuery('.sink1-display').css('height');
        sinkheight = removepx(sinkheight);
        sinkheight = Number(sinkheight);
       var top = jQuery('.center1').css('top');
       top = removepx(top);

       // max is 7.5
        
        sinktop = roundme(((sinktop+(sinkheight/2))/8)-3.5);
        slabmin = roundme(((sinkheight/2)/8)+1);
        if (slabmin < 4.5){
            slabmin = 4.5;
        }
        
       
        jQuery('.center1').offset().top
       if (move == 'add'){
            
            height = height/8;
            height = height+.25;
            height = height*8;

   
                top = top-2;                
                faucet1 = faucet1+.25;
                
          
            
            if (faucet1 <= sinktop){
                jQuery('.faucet1-distance').val(faucet1);
                jQuery('.lineinfo1').html(faucet1+'"');
                jQuery('.center1').css('height',height+'px');
                jQuery('.center1').css('top',top+'px');
            }else{
                jQuery('.cp-message p').prepend('<span class="cpalert">This is the furthest distance possible for your dimensions. </span>');
            }
          

        }
        
        if (move == 'minus'){
            top = top+2;
            height = height/8;
            height = height-.25;
            height = height*8;

            if (height < slabmin){
                height = slabmin;
                top=top-2;
            }else{
                faucet1 = faucet1-.25;
            }


            if (faucet1 >= slabmin){
            jQuery('.faucet1-distance').val(faucet1);
            jQuery('.lineinfo1').html(faucet1+'"');
            jQuery('.center1').css('height',height+'px');
            jQuery('.center1').css('top',top+'px');
            }

        }

        

    }

    if (!jQuery('.degree1-right').hasClass('degreehover')){
        var line1height = jQuery('.right45 .line1').css('height');
        line1height = removepx(line1height);
        var line1width = jQuery('.right45 .line1').css('width');
        line1width = removepx(line1width);
        var right45top = jQuery('.right45').css('top');
        right45top = removepx(right45top);
        var right45left = jQuery('.right45').css('left');
        right45left = removepx(right45left);

        // figure out distance to the top
        var sinkholetop = jQuery('.sinkholeright1').css('top');
        sinkholetop = removepx(sinkholetop);
        var sinkholeright = jQuery('.sinkholeright1').css('left');
        sinkholeright = removepx(sinkholeright);        
        var maxlimit = (holetop+(holeheight/2))-(line1height-sinkholetop);

        if (move == 'add' && maxlimit > 20){
            line1height = line1height+2;
            line1width = line1width+2;

            var halftop = sinkholetop-2;
            var halfslab = sinkholeright+2;
            
            faucet1 = faucet1+.25;

            
                jQuery('.faucet1-distance').val(faucet1);
                jQuery('.lineinfo1').html(faucet1+'"');
                //jQuery('.right45 .line1').css('height', line1height+'px');
                jQuery('.right45 .line1').css('width', line1width+'px');
                jQuery('.sinkholeright1').css('top', halftop);
                jQuery('.sinkholeright1').css('left', halfslab+'px');
                        
            
        }else if (move == 'add' && maxlimit <= 20){
            jQuery('.cp-message p').prepend('<span class="cpalert">This is the furthest distance possible for your dimensions. </span>');
        }

        if (move == 'minus'){
            line1height = line1height-2;
            line1width = line1width-2;

            
            halfslab = line1width-(holeheight/2);
            var halftop = sinkholetop+2;
            var halfslab = sinkholeright-2;
            faucet1 = faucet1-.25;

            console.log('faucet1:'+faucet1+' slabmin:'+slabmin);
            if (faucet1 >= slabmin){
                jQuery('.faucet1-distance').val(faucet1);
                jQuery('.lineinfo1').html(faucet1+'"');
                //jQuery('.line1').css('height', line1height+'px');
                jQuery('.right45 .line1').css('width', line1width+'px');
                jQuery('.sinkholeright1').css('top', halftop+'px');
                jQuery('.sinkholeright1').css('left', halfslab+'px');
            }
        } 

        var holelen = faucet1.toString().length;
        holelen = (holelen*4)+14;
        jQuery('.lineinfo1').css('left','-'+holelen+'px');       
    } 

   jQuery('.faucet1dis').html(faucet1); 
}

function moveFaucet2(move){

    var slabwidth = jQuery('.cp-counter').css('width');
    slabwidth = removepx(slabwidth);
    var slabheight = jQuery('.cp-counter').css('height');
    slabheight = removepx(slabheight);
    var holeheight = jQuery('.sink2-display').css('height');
    holeheight = removepx(holeheight);
    var holetop = jQuery('.sink2-display').css('top');
    holetop = removepx(holetop);    
    var halfslab = slabwidth/2;
    var slabmax = slabheight-20;
    var slabmin = ((holeheight/2)/8)+1;
    if (slabmin < 4.5){
        slabmin = 4.5;
    } 

    var faucet1 = Number(jQuery('.faucet2-distance').val());


    if (!jQuery('.degree2-left').hasClass('degreehover')){
        var line1height = jQuery('.left452 .line1').css('height');
        line1height = removepx(line1height);
        var line1width = jQuery('.left452 .line1').css('width');
        line1width = removepx(line1width);
        var left45top = jQuery('.left452').css('top');
        left45top = removepx(left45top);
        var left45left = jQuery('.left452').css('left');
        left45left = removepx(left45left);

        // figure out distance to the top
        var sinkholetop = jQuery('.sinkholeleft2').css('top');
        sinkholetop = removepx(sinkholetop);
        var maxlimit = (holetop+(holeheight/2))-(line1height-sinkholetop);

        
        //console.log('maxlimit:'+maxlimit+' slabmax:'+slabmax+' holeheight:'+holeheight+' holetop:'+holetop+' line1height:'+line1height+' sinkholetop:'+sinkholetop);

        if (move == 'add' && maxlimit > 20){
            line1height = line1height+2;
            line1width = line1width+2;

            halfslab = line1width-7;
            var halftop = line1height+2;
            faucet1 = faucet1+.25;

                jQuery('.faucet2-distance').val(faucet1);
                jQuery('.lineinfo2').html(faucet1+'"');
                jQuery('.left452 .line1').css('height', line1height+'px');
                jQuery('.left452 .line1').css('width', line1width+'px');
                jQuery('.left452').css('top', '-'+halftop+'px');
                jQuery('.left452').css('left', '-'+halfslab+'px');
 
        }

        if (move == 'minus'){
            line1height = line1height-2;
            line1width = line1width-2;

            
            halfslab = line1width-7;
            var halftop = line1height+2;
            faucet1 = faucet1-.25;

            console.log('faucet2:'+faucet1+' slabmin:'+slabmin);
            if (faucet1 >= slabmin){
               
                jQuery('.faucet2-distance').val(faucet1);
                jQuery('.lineinfo2').html(faucet1+'"');
                jQuery('.left452 .line1').css('height', line1height+'px');
                jQuery('.left452 .line1').css('width', line1width+'px');
                jQuery('.left452').css('top', '-'+halftop+'px');
                jQuery('.left452').css('left', '-'+halfslab+'px');
            }
        }

    }

    if (!jQuery('.degree2-center').hasClass('degreehover')){
       var height = jQuery('.center2').css('height');
       height = removepx(height);
       var sinktop = jQuery('.sink2-display').css('top');
       sinktop = removepx(sinktop);
       sinktop = Number(sinktop);
       var sinkheight = jQuery('.sink2-display').css('height');
       sinkheight = removepx(sinkheight);
       sinkheight = Number(sinkheight);
       var top = jQuery('.center2').css('top');
       top = removepx(top);

       sinktop = roundme(((sinktop+(sinkheight/2))/8)-3.5);
        slabmin = roundme(((sinkheight/2)/8)+1);
        if (slabmin < 4.5){
            slabmin = 4.5;
        }
        
       if (move == 'add'){
            
            height = height/8;
            height = height+.25;
            height = height*8;

   
                top = top-2;                
                faucet1 = faucet1+.25;
                
            if (faucet1 <= sinktop){
               
                jQuery('.faucet2-distance').val(faucet1);
                jQuery('.lineinfo2').html(faucet1+'"');
                jQuery('.center2').css('height',height+'px');
                jQuery('.center2').css('top',top+'px');
            }
          

        }
        
        if (move == 'minus'){
            top = top+2;
            height = height/8;
            height = height-.25;
            height = height*8;

            if (height < slabmin){
                height = slabmin;
                top=top-2;
            }else{
                faucet1 = faucet1-.25;
            }


            if (faucet1 >= slabmin){
               
            jQuery('.faucet2-distance').val(faucet1);
            jQuery('.lineinfo2').html(faucet1+'"');
            jQuery('.center2').css('height',height+'px');
            jQuery('.center2').css('top',top+'px');
            }

        }

        

    }

    if (!jQuery('.degree2-right').hasClass('degreehover')){
        var line1height = jQuery('.right452 .line1').css('height');
        line1height = removepx(line1height);
        var line1width = jQuery('.right452 .line1').css('width');
        line1width = removepx(line1width);
        var right45top = jQuery('.right452').css('top');
        right45top = removepx(right45top);
        var right45left = jQuery('.right452').css('left');
        right45left = removepx(right45left);

        // figure out distance to the top
        var sinkholetop = jQuery('.sinkholeright2').css('top');
        sinkholetop = removepx(sinkholetop);
        var sinkholeright = jQuery('.sinkholeright2').css('left');
        sinkholeright = removepx(sinkholeright);        
        var maxlimit = (holetop+(holeheight/2))-(line1height-sinkholetop);

        console.log('maxlimit:'+maxlimit+' slabmax:'+slabmax);

        if (move == 'add' && maxlimit > 20){
            line1height = line1height+2;
            line1width = line1width+2;

            var halftop = sinkholetop-2;
            var halfslab = sinkholeright+2;
            
            faucet1 = faucet1+.25;

         
                jQuery('.faucet2-distance').val(faucet1);
                jQuery('.lineinfo2').html(faucet1+'"');
                //jQuery('.right45 .line1').css('height', line1height+'px');
                jQuery('.right452 .line1').css('width', line1width+'px');
                jQuery('.sinkholeright2').css('top', halftop);
                jQuery('.sinkholeright2').css('left', halfslab+'px');
                        
            
        }

        if (move == 'minus'){
            line1height = line1height-2;
            line1width = line1width-2;

            
            halfslab = line1width-7;
            var halftop = sinkholetop+2;
            var halfslab = sinkholeright-2;
            faucet1 = faucet1-.25;

            console.log('faucet1:'+faucet1+' slabmin:'+slabmin);
            if (faucet1 >= slabmin){
                
                jQuery('.faucet2-distance').val(faucet1);
                jQuery('.lineinfo2').html(faucet1+'"');
                //jQuery('.line1').css('height', line1height+'px');
                jQuery('.right452 .line1').css('width', line1width+'px');
                jQuery('.sinkholeright2').css('top', halftop+'px');
                jQuery('.sinkholeright2').css('left', halfslab+'px');
            }
        } 

        var holelen = faucet1.toString().length;
        holelen = (holelen*4)+14;
        jQuery('.lineinfo2').css('left','-'+holelen+'px');           
    }    

  jQuery('.faucet2dis').html(faucet1);  
}

// figure out slab with and give user info about how many sinks they can have

function checkWidth(){
    //14.75" x 2 = 29.5 (2 bowls) + 8 (eitherside) + 8 (between) = 45.5 total width needed

    var slabwidth = jQuery('input[name=slabwidth]').val();
    if (slabwidth > 63){
         jQuery('.sinks .cp-message').html('Your width of '+slabwidth+'" can accommodate: 2 sinks');
    }else{

        jQuery('#sinkmessage').html('Your width of '+slabwidth+'" can accommodate: 1 sink');

    }

}

function reorderSlabs(){
    var idstore = [];
    var sortid = '';
    var currentid = '';
    var idstore2 = [];

    jQuery('#stone .top-slabs .slab-images > div').map(function() {
        sortid = jQuery("#"+this.id).attr('sortorder');
        idstore[sortid]=this.id;
        //idstore.push(this.id);
    });
    idstore = idstore.filter(function(n){ return n != undefined }); 
    for (i = 0; i < idstore.length; i++) { 
        if (i != 0){
            jQuery('#'+idstore[i]).insertAfter('#'+currentid);
        }
        currentid = idstore[i];
    }
 

     jQuery('#stone .fullslabs .slab-images > div').map(function() {
        sortid = jQuery("#"+this.id).attr('sortorder');
        idstore2[sortid]=this.id;
        //idstore.push(this.id);
    });
    idstore2 = idstore2.filter(function(n){ return n != undefined }); 
    for (i = 0; i < idstore2.length; i++) { 
        if (i != 0){
            jQuery('#'+idstore2[i]).insertAfter('#'+currentid);
        }
        currentid = idstore2[i];
    }
      

    return;
}

function orderSample(sku, endinfo){
    jQuery('.samplecheck').html(1);
       var scope = angular.element(document.getElementById("cp-controller")).scope();
    scope.$apply(function () {
       scope.setSample(sku, endinfo);
    });  

}

function netsuite(){

    jQuery('#emailForm').submit(function() {
        window.open('', 'formpopup', 'width=400,height=100');
        this.target = 'formpopup';
    });
}

// pass variable from angular to javascript.
//jQuery('[ng-controller="project"]').scope().sink1left;

jQuery(document).ready(function(){
    jQuery("#width").numeric();
    jQuery("#depth").numeric();
});
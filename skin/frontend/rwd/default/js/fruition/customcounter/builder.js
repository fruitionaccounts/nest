var builder = angular.module('builder', []);

builder.controller('builderController', ['$scope', '$http', '$parse', function ($scope, $http, $parse) {
    $scope.productsLoaded = false;
    $scope.loading        = false;
    $scope.cartLoading = false;
    $scope.message = '';
    $scope.stoneWidth = 61;
    $scope.stoneDepth = 22;

    $scope.submit = function (diagram, print) {
        if(print) {
            var PrintDiagram;
            printDiagram = window.open('', '_blank');
        }
        $scope.cartLoading = true;
        $http.post(getUrl()+'customcounter/index/addProducts',
            {
                width: Math.ceil(parseFloat($scope.stoneWidth)),
                depth: Math.ceil(parseFloat($scope.stoneDepth)),
                thickness: $scope.thickness,
                thicknessLabel: $scope.thicknessLabel,
                slab: $scope.slabProductId,
                isRemnant: $scope.remnantFlag,
                slabName: $scope.slabName,
                sinkName: $scope.sinkName,
                edgeName: $scope.edge.short_description,
                slabBg: $scope.slabBg,
                isDiagram: diagram,
                diagramId: $scope.diagramId,
                polish: {
                    total: $scope.totalPolish,
                    id: $scope.edge.entity_id,
                    sideLeft: $scope.polish.left,
                    sideRight: $scope.polish.right,
                    sideBack: $scope.polish.back,
                    sideFront: 1
                },
                backsplash: {
                    total: $scope.totalBacksplash,
                    id: $scope.backsplashId,
                    sideLeft: $scope.backsplash.left,
                    sideRight: $scope.backsplash.right,
                    sideBack: $scope.backsplash.back
                },
                sinks: {
                    sinkProductId: $scope.selectedSink.entity_id,
                    sinkSku: $scope.sinkSku,
                    sinkCount: $scope.sinkCount,
                    sink1VesselRadius: $scope.sink1Hole,
                    sink2VesselRadius: $scope.sink2Hole,
                    sink1Left: $scope.sinkPos1,
                    sink2Left: $scope.sinkPos2,
                    sink1Back: $scope.sinkVertPos1,
                    sink2Back: $scope.sinkVertPos2,
                    css: {
                        sink1CSSLeft: $scope.sink1CSSLeft,
                        sink1CSSTop: $scope.sink1CSSTop,
                        selectedSink1Width: $scope.selectedSink1Width,
                        selectedSink1Depth: $scope.selectedSink1Depth,
                        sinkRadius1: $scope.sinkRadius1,
                        sinkmeasure1top: $scope.sinkmeasure1top,
                        sinkmeasure1width: $scope.sinkmeasure1width,
                        sink2CSSLeft: $scope.sink2CSSLeft,
                        sink2CSSTop: $scope.sink2CSSTop,
                        selectedSink2Width: $scope.selectedSink2Width,
                        selectedSink2Depth: $scope.selectedSink2Depth,
                        sinkRadius2: $scope.sinkRadius2,
                        sinkmeasure2top: $scope.sinkmeasure2top,
                        sinkmeasure2width: $scope.sinkmeasure2width
                    }
                },
                faucets: {
                    faucet1: $scope.faucet1Option,
                    faucet2: $scope.faucet2Option,
                    faucetId1: $scope.faucet1Id,
                    faucetId2: $scope.faucet2Id,
                    faucet1distance: $scope.faucetCenter1,
                    faucet2distance: $scope.faucetCenter2,
                    css: {
                        faucet11top: $scope.faucet11top,
                        faucet11left: $scope.faucet11left,
                        faucet12top: $scope.faucet12top,
                        faucet12left: $scope.faucet12left,
                        faucet12bottom: $scope.faucet12bottom,
                        faucet12right: $scope.faucet12right,
                        cssFaucetCenter1: $scope.cssFaucetCenter1,
                        faucet1Rotate: $scope.faucet1Rotate,
                        faucet13top: $scope.faucet13top,
                        faucet13left: $scope.faucet13left,
                        faucet21top: $scope.faucet21top,
                        faucet21left: $scope.faucet21left,
                        faucet22top: $scope.faucet22top,
                        faucet22left: $scope.faucet22left,
                        faucet22bottom: $scope.faucet22bottom,
                        faucet22right: $scope.faucet22right,
                        cssFaucetCenter2: $scope.cssFaucetCenter2,
                        faucet2Rotate: $scope.faucet2Rotate,
                        faucet23top: $scope.faucet23top,
                        faucet23left: $scope.faucet23left
                    }
                }
            }
        ).then(
            function successCallback(response) {
                if(!diagram) {
                    window.location.href = getUrl()+'checkout/cart/';
                }else{
                    $scope.cartLoading = false;
                }
                if(print){
                    printDiagram.location = getUrl()+"customcounter/index/print/id/"+$scope.diagramId;
                }
            }, function errorCallback(response) {
                console.log('Adding products produced the following error: ' + response);
            }
        );
    };

    $scope.loadProducts = function () {
        $scope.loading = true;
        $http({
            method: 'GET',
            url:    getUrl()+'customcounter/index/products'
        }).then(
            function successCallback(response) {
                //localStorage.setItem('allProducts', response)
                $scope.products       = angular.fromJson(response);
                $scope.productData    = $scope.products.data;
                $scope.productsArray  = [];
                $scope.productsLoaded = true;
                $scope.loading        = false;
                $scope.attributeSetCheck('Vanity Slabs', 'vanitySlabs');
                angular.forEach($scope.productData, function (value, key) {
                    if(value.price_group != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'price_group') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.price_group) {
                                        value['price_group_label'] = v.label;
                                    }
                                });

                            }
                        });
                    }
                    if(value.stone != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'stone') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.stone) {
                                        value['stone_label'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.sink_width != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'sink_width') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.sink_width) {
                                        value['label_sink_width'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.sink_depth != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'sink_depth') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.sink_depth) {
                                        value['label_sink_depth'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.sink_type != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'sink_type') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.sink_type) {
                                        value['label_sink_type'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.sink_material != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'sink_material') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.sink_material) {
                                        value['label_sink_material'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.min_width_1 != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'min_width_1') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.min_width_1) {
                                        value['label_min_width_1'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    if(value.min_width_2 != ""){
                        angular.forEach($scope.productData.prepared_attributes, function (value2, key2) {
                            if (key2 == 'min_width_2') {
                                angular.forEach(value2, function (v, k) {
                                    if (v.value == value.min_width_2) {
                                        value['label_min_width_2'] = v.label;
                                    }
                                });
                            }
                        });
                    }
                    $scope.productsArray[key] = value;
                });
                angular.forEach($scope.productData.prepared_attributes['thickness'], function (value, key){
                   if(value.label == '2 cm or 3 cm'){
                       $scope.bothThickness = value.value;
                   }
                });
            }, function errorCallback(response) {
                console.log('Loading products produced the following error: ' + response);
            }
        );
    };
    $scope.thicknessFilter = function(object){
        return object.thickness == $scope.thickness || object.thickness == $scope.bothThickness;
    };
    $scope.zeroEvaluate = function(filterKey, value){
        if(filterKey == 'thickness') return true;
        var i = 0;
        angular.forEach($scope.filteredCollection, function(val, key){
            if($scope.dimensions($scope.stoneWidth, $scope.stoneDepth, val)){
                valRef = val[filterKey];
                if(valRef == value){
                    i = i + 1;
                }
            }
        });
        if(i>0){
            return true;
        }else{
            return false;
        }
    };
    $scope.attributeSetCheck = function (attributeSetName, attrScope) {
        if ($scope.productsLoaded == false) {
            return;
        }
        angular.forEach($scope.productData.attribute_sets, function (value, key) {
            if (attributeSetName == value) {
                $parse(attrScope).assign($scope, key);
                return;
            }
        });
    };

    $scope.proceed = function(tab){
        $scope.message = '';
        if(tab == 'dimensionsTab'){
            $scope.dimensionsTab = 1;
            $scope.stonesTab = 0;
            $scope.edgingTab = 0;
            $scope.sinksTab = 0;
            $scope.faucetsTab = 0;
            $scope.priceTab = 0;
        }
        if(tab == 'stonesTab'){
            if(typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined'){
                $scope.dimensionsTab = 0;
                $scope.stonesTab = 1;
                $scope.edgingTab = 0;
                $scope.sinksTab = 0;
                $scope.faucetsTab = 0;
                $scope.priceTab = 0;
            }else{
                $scope.message = 'Please ensure you have specified the slab height, width, and thickness.';
            }
        }
        if(tab == 'edgingTab'){
            if($scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined'){
                $scope.dimensionsTab = 0;
                $scope.stonesTab = 0;
                $scope.edgingTab = 1;
                $scope.sinksTab = 0;
                $scope.faucetsTab = 0;
                $scope.priceTab = 0;
            }else{
                $scope.message = 'Please ensure you have specified the slab height, width, thickness and selected a slab.';
            }
        }
        if(tab == 'sinksTab'){
            if($scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined'){
                $scope.dimensionsTab = 0;
                $scope.stonesTab = 0;
                $scope.edgingTab = 0;
                $scope.sinksTab = 1;
                $scope.faucetsTab = 0;
                $scope.priceTab = 0;
            }else{
                $scope.message = 'Please ensure you have specified the slab height, width, thickness, selected a slab, and selected your edge type.';
            }
        }
        if(tab == 'faucetsTab'){
            if($scope.sinkCount == 0 && $scope.sinkCount != '' && $scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined' || $scope.sinkCount > 0 && $scope.sinkCount != '' && $scope.sinkName != '' && $scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined'){
                $scope.dimensionsTab = 0;
                $scope.stonesTab = 0;
                $scope.edgingTab = 0;
                $scope.sinksTab = 0;
                $scope.faucetsTab = 1;
                $scope.priceTab = 0;
            }else{
                $scope.message = 'Please ensure you have specified the slab height, width, thickness, selected a slab, selected your edge type and chosen a sink type, or chosen "No Sink".';
            }
        }
        if(tab == 'priceTab'){
            if($scope.sinkCount == 0 && $scope.sinkCount != '' && $scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined' || $scope.sinkCount == 1 && $scope.sinkCount != '' && $scope.sinkName != '' && $scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined' && $scope.faucet1Option != '' || $scope.sinkCount == 2 && $scope.sinkCount != '' && $scope.sinkName != '' && $scope.edge != '' && $scope.slabName != '' && typeof eval($scope.stoneWidth) != 'undefined' && typeof eval($scope.stoneDepth) != 'undefined' && typeof eval($scope.thickness) != 'undefined' && $scope.faucet1Option != '' && $scope.faucet2Option != ''){
                $scope.dimensionsTab = 0;
                $scope.stonesTab = 0;
                $scope.edgingTab = 0;
                $scope.sinksTab = 0;
                $scope.faucetsTab = 0;
                $scope.priceTab = 1;
            }else{
                $scope.message = 'Please ensure you have specified the slab height, width, thickness, selected a slab, selected your edge type, chosen a sink type, or chosen "No Sink" and chosen a faucet type or no faucets.';
            }
        }
    };


    $scope.dimensions = function (width, depth, reference) {
        if ($scope.productsLoaded == false) {
            return;
        }
        if (width === undefined || width === null || depth === undefined || depth === null) {
            return true;
        }
        isFalse    = 0;
        error      = '';
        successMsg = '';
        angular.forEach($scope.productData.prepared_attributes, function (value, key) {
            if (key == 'stone_min_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_min_width) {
                        if (Number(v.label) > Number(width)) {
                            isFalse = 1;
                            error   = error + 'user width: ' + width + ' < product min width ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' => product min width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'stone_max_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_max_width) {
                        if (Number(v.label) < Number(width)) {
                            isFalse = 1;
                            error   = error + 'user width: ' + width + ' > products max width: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' <= product max width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'stone_min_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_min_depth) {
                        if (Number(v.label) > Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' < product min depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' => product min depth: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'stone_max_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_max_depth) {
                        if (Number(v.label) < Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' > product max depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' <= product max depth: ' + v.label + ', ';
                        }
                    }
                });
            }

        });
        if(reference.qty < width){
            isFalse = 1;
            error = reference.name + ' has insufficient quantity.';
        }
        if (isFalse > 0) {
            //console.log(reference.name + ' was rejected because '+error);
            return false;
        } else {
            //console.log(reference.name + ' was accepted because '+successMsg);
            return true;
        }
    };

    $scope.rdimensions = function (width, depth, reference) {
        if ($scope.productsLoaded == false) {
            return;
        }
        if (width === undefined || width === null || depth === undefined || depth === null) {
            return true;
        }
        isFalse    = 0;
        error      = '';
        successMsg = '';
        angular.forEach($scope.productData.prepared_attributes, function (value, key) {
            if (key == 'remnant_min_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.remnant_min_width) {
                        if (Number(v.label) > Number(width)) {
                            isFalse = 1;
                            error   = error + 'user width: ' + width + ' < product min width ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' => product min width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'remnant_max_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.remnant_max_width) {
                        if (Number(v.label) < Number(width)) {
                            isFalse = 1;
                            error   = error + 'user width: ' + width + ' > products max width: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' <= product max width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'remnant_min_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.remnant_min_depth) {
                        if (Number(v.label) > Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' < product min depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' => product min depth: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'remnant_max_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.remnant_max_depth) {
                        if (Number(v.label) < Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' > product max depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' <= product max depth: ' + v.label + ', ';
                        }
                    }
                });
            }

        });
        if(reference.qty < width){
            isFalse = 1;
            error = reference.name + ' has insufficient quantity.';
        }
        if (isFalse > 0) {
            //console.log(reference.name + ' was rejected because '+error);
            return false;
        } else {
            //console.log(reference.name + ' was accepted because '+successMsg);
            return true;
        }
    };

    $scope.edgeDimensions = function (width, depth, reference) {
        if ($scope.productsLoaded == false) {
            return;
        }
        if (width === undefined || width === null || depth === undefined || depth === null) {
            return true;
        }
        isFalse    = 0;
        error      = '';
        successMsg = '';
        angular.forEach($scope.productData.prepared_attributes, function (value, key) {
            if (key == 'stone_min_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_min_width) {
                        if (Number(v.label) > Number(width)) {
                            isFalse = 1;
                            error   = error + 'user width: ' + width + ' < product min width ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' => product min width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'stone_min_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.stone_min_depth) {
                        if (Number(v.label) > Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' < product min depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' => product min depth: ' + v.label + ', ';
                        }
                    }
                });
            }
        });
        if (isFalse > 0) {
            //console.log(reference.name + ' was rejected because '+error);
            return false;
        } else {
            //console.log(reference.name + ' was accepted because '+successMsg);
            return true;
        }
    };

    $scope.sinkDimensions = function (width, depth, reference, sinkCount) {
        if ($scope.productsLoaded == false) {
            return;
        }
        if (width === undefined || width === null || depth === undefined || depth === null) {
            return true;
        }
        if (sinkCount == 0 || reference.sku == 'LAB-CUT-VES') {
            return true;
        }
        isFalse    = 0;
        error      = '';
        successMsg = '';
        angular.forEach($scope.productData.prepared_attributes, function (value, key) {
            if (key == 'min_width_1' && sinkCount == 1) {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.min_width_1) {
                        if (Number(v.label) > Number(width)) {
                            if($scope.sinkCount == 1) {
                                isFalse = 1;
                                error = error + 'user width: ' + width + ' < 1 sink min width ' + v.label + ', ';
                            }
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' => 1 sink min width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'min_width_2' && sinkCount == 2) {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.min_width_2) {
                        if (Number(v.label) > Number(width)) {
                            if($scope.sinkCount==2) {
                                isFalse = 1;
                                error   = error + 'user width: ' + width + ' < 2 sinks min width: ' + v.label + ', ';
                            }
                        } else {
                            successMsg = successMsg + 'user width: ' + width + ' => 2 sinks min width: ' + v.label + ', ';
                        }
                    }
                });
            }
            if (key == 'min_depth_sink') {
                angular.forEach(value, function (v, k) {
                    if (v.value == reference.min_depth_sink) {
                        if (Number(v.label) > Number(depth)) {
                            isFalse = 1;
                            error   = error + 'user depth: ' + depth + ' < sink min depth: ' + v.label + ', ';
                        } else {
                            successMsg = successMsg + 'user depth: ' + depth + ' => sink min depth: ' + v.label + ', ';
                        }
                    }
                });
            }
        });
        if (isFalse > 0) {
            //console.log(reference.name + ' was rejected because '+error);
            return false;
        } else {
            //console.log(reference.name + ' was accepted because '+successMsg);
            return true;
        }
    };

    $scope.thisSlab = function (product_id, index) {

        $scope.edgingLoading = true;
        $scope.edgingLoaded  = false;
        $scope.slabSelected  = index;
		$scope.slabProductId = product_id;

        if($scope.stoneWidth < 22 || $scope.stoneDepth < 21){
            $scope.sinkCount = 0;
        }

        if($scope.stoneWidth < 44 || $scope.stoneDepth == 21){
            $scope.sinkCount = 1;
        }

        if($scope.stoneWidth >= 44 && $scope.stoneDepth > 21 || $scope.stoneWidth >= 44) {
            $scope.sinkCount = 2;
        }

        if ($scope.productData[index].special_price && $scope.productData[index].special_price != null) {
            selectedStonePrice = $scope.productData[index].special_price;
        } else {
            selectedStonePrice = $scope.productData[index].price;
        }

        //Pricing Calculations
        $scope.price.slab = parseFloat(selectedStonePrice) * Math.ceil(parseFloat($scope.stoneWidth));

        $http.post(getUrl()+'customcounter/index/productupsells', {product_id: product_id})
            .then(
                function successCallback(response) {
                    //localStorage.setItem('allProducts', response)
                    $scope.upsellIds = angular.fromJson(response);
                    $scope.upsells   = [];
                    $scope.attributeSetCheck('Edging', 'edging');
                    $scope.edgingLoaded  = true;
                    $scope.edgingLoading = false;
                    angular.forEach($scope.upsellIds, function (value, key) {
                        $scope.upsells[value] = key;
                    });
                    $scope.upsellArray = [];
                    angular.forEach($scope.productData, function (value, key) {
                        if (value.entity_id in $scope.upsells) {
                            $scope.upsellArray[key] = value;
                        }
                    });
                }, function errorCallback(response) {
                    console.log('Loading upsell ids produced the following error: ' + response);
                }
            );

    };

    $scope.orderSample = function(sampleSku){
        $http.post(getUrl()+'customcounter/index/ordersample', {sku: sampleSku})
            .then(
                function successCallback() {
                    $scope.message = "Your slab sample was added to the cart.";
                }, function errorCallback(response) {
                    $scope.message = 'Error: Could not add sample to cart. '+response;
                }
            );
    };

    $scope.processEdge = function () {
        //need side count multiplied by side inches to get true qty
		totalPolish = Math.ceil(parseFloat($scope.stoneWidth));

        if($scope.polish.back){
            totalPolish = totalPolish + Math.ceil(parseFloat($scope.stoneWidth));
        }

        if($scope.polish.right){
            totalPolish = totalPolish + Math.ceil(parseFloat($scope.stoneDepth));
        }

        if($scope.polish.left){
            totalPolish = totalPolish + Math.ceil(parseFloat($scope.stoneDepth));
        }

        $scope.totalPolish = totalPolish;

        if ($scope.edge.special_price != null) {
            edgePrice = $scope.edge.special_price;
        } else {
            edgePrice = $scope.edge.price;
        }

        $parse('price.polish').assign($scope);
        $scope.price.polish = Math.ceil(parseFloat($scope.totalPolish)) * parseFloat(edgePrice);

        totalBacksplash = 0;
        if($scope.backsplash.back){
            totalBacksplash = totalBacksplash + Math.ceil(parseFloat($scope.stoneWidth));
        }

        if($scope.backsplash.right){
            totalBacksplash = totalBacksplash + Math.ceil(parseFloat($scope.stoneDepth));
        }

        if($scope.backsplash.left){
            totalBacksplash = totalBacksplash + Math.ceil(parseFloat($scope.stoneDepth));
        }
        $scope.totalBacksplash = totalBacksplash;

        angular.forEach($scope.upsellArray, function (value) {
            $scope.backsplashSku = value.sku;
            $scope.backsplashId = value.entity_id;
            if ($scope.edge.special_price && $scope.edge.special_price != null) {
                backsplashPrice = value.special_price;
            } else {
                backsplashPrice = value.price;
            }
        });
        $parse('price.backsplash').assign($scope);
        $scope.price.backsplash = Math.ceil(parseFloat($scope.totalBacksplash)) * parseFloat(backsplashPrice);
        $scope.proceed('sinksTab');
    };

    $scope.selectSink = function (sink) {

        $scope.resets(stoneReset = 0, edgingReset = 0, sinksReset = 0, faucetsReset = 1);

        $scope.getSinkWD(sink.sink_width, sink.sink_depth);
        $scope.sinkSku      = sink.sku;
        $scope.selectedSink = sink;
        // (Math...)/1 converts .toFixed result back to a number
        if ($scope.sinkCount == 1) {
            $scope.sinkPos1     = ((Math.round((Number($scope.stoneWidth) * .5) * 4) / 4).toFixed(2))/1;
            $scope.sinkVertPos1 = ((Math.round((Number($scope.stoneDepth) / 2) * 4) / 4).toFixed(2))/1;
        }
        if ($scope.sinkCount == 2) {
            $scope.sinkPos1 = ((Math.ceil((Number($scope.stoneWidth) * .25) * 4) / 4).toFixed(2))/1;
            $scope.sinkPos2 = ((Math.floor((Number($scope.stoneWidth) * .75) * 4) / 4).toFixed(2))/1;
            $scope.sinkVertPos1 = ((Math.round((Number($scope.stoneDepth) / 2) * 4) / 4).toFixed(2))/1;
            $scope.sinkVertPos2 = ((Math.round((Number($scope.stoneDepth) / 2) * 4) / 4).toFixed(2))/1;
        }

        if ($scope.sinkSku == 'LAB-CUT-VES') {
            $scope.selectedSink1Width = 1.75;
            $scope.selectedSink1Depth = 1.75;
            $scope.sink1Hole = 1.75;
            $scope.faucetCenter1 = (Number($scope.sink1Hole)/2)+1;

            if ($scope.sinkCount == 2) {
                $scope.selectedSink2Width = 1.75;
                $scope.selectedSink2Depth = 1.75;
                $scope.sink2Hole = 1.75;
                $scope.faucetCenter2 = (Number($scope.sink2Hole)/2)+1;
            }
        } else {
            $scope.selectedSink1Width = $scope.sinksWidth;
            $scope.selectedSink1Depth = $scope.sinksDepth;
            if ($scope.sinkCount == 2) {
                $scope.selectedSink2Width = $scope.sinksWidth;
                $scope.selectedSink2Depth = $scope.sinksDepth;
            }
        }

        $scope.setSinkCSS($scope.stoneWidth, $scope.stoneDepth, $scope.selectedSink1Width, $scope.selectedSink1Depth, $scope.selectedSink2Width, $scope.selectedSink2Depth, $scope.sinkPos1, $scope.sinkPos2, $scope.sinkVertPos1, $scope.sinkVertPos2);

    };
    $scope.limitSink1 = function(input){
        if(parseFloat(input.target.value)<parseFloat($scope.sinkMin1)) $scope.sinkPos1 = parseFloat($scope.sinkMin1);
        if(parseFloat(input.target.value)>parseFloat($scope.sinkMax1)) $scope.sinkPos1 = parseFloat($scope.sinkMax1);
        $scope.sinkPos1 = parseFloat((Math.round(parseFloat($scope.sinkPos1) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink1');
    };
    $scope.limitSink1Vert = function(input){
        if(parseFloat(input.target.value)<parseFloat($scope.sink1MinDepth)) $scope.sinkVertPos1 = parseFloat($scope.sink1MinDepth);
        if(parseFloat(input.target.value)>parseFloat($scope.sink1MaxDepth)) $scope.sinkVertPos1 = parseFloat($scope.sink1MaxDepth);
        $scope.sinkVertPos1 = parseFloat((Math.round(parseFloat($scope.sinkVertPos1) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink1vert', $scope.sinkVertPos1, $scope.sinkVertPos2, $scope.sink1Hole, $scope.sink2Hole);
    };
    $scope.limitSink1Hole = function(input){
        if(parseFloat(input.target.value)<1.75) $scope.sink1Hole = 1.75;
        if(parseFloat(input.target.value)>parseFloat($scope.sink1HoleMax)) $scope.sink1Hole = parseFloat($scope.sink1HoleMax);
        $scope.sink1Hole = parseFloat((Math.round(parseFloat($scope.sink1Hole) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink1Hole', $scope.sinkVertPos1, $scope.sinkVertPos2, $scope.sink1Hole, $scope.sink2Hole);
    };
    $scope.limitSink2 = function(input){
        if(parseFloat(input.target.value)<parseFloat($scope.sinkMin2)) $scope.sinkPos2 = parseFloat($scope.sinkMin2);
        if(parseFloat(input.target.value)>parseFloat($scope.sinkMax2)) $scope.sinkPos2 = parseFloat($scope.sinkMax2);
        $scope.sinkPos2 = parseFloat((Math.round(parseFloat($scope.sinkPos2) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink2');
    };
    $scope.limitSink2Vert = function(input){
        if(parseFloat(input.target.value)<parseFloat($scope.sink2MinDepth)) $scope.sinkVertPos2 = parseFloat($scope.sink2MinDepth);
        if(parseFloat(input.target.value)>parseFloat($scope.sink2MaxDepth)) $scope.sinkVertPos2 = parseFloat($scope.sink2MaxDepth);
        $scope.sinkVertPos2 = parseFloat((Math.round(parseFloat($scope.sinkVertPos2) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink2vert', $scope.sinkVertPos1, $scope.sinkVertPos2, $scope.sink1Hole, $scope.sink2Hole);
    };
    $scope.limitSink2Hole = function(input){
        if(parseFloat(input.target.value)<1.75) $scope.sink2Hole = 1.75;
        if(parseFloat(input.target.value)>parseFloat($scope.sink2HoleMax)) $scope.sink2Hole = parseFloat($scope.sink2HoleMax);
        $scope.sink2Hole = parseFloat((Math.round(parseFloat($scope.sink2Hole) * 4) / 4).toFixed(2));
        $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount, 'sink2Hole', $scope.sinkVertPos1, $scope.sinkVertPos2, $scope.sink1Hole, $scope.sink2Hole);
    };
    $scope.setSinkCount = function (sink) {
        if (sink != undefined) {
            $scope.selectSink(sink);
            $scope.sinkPosition($scope.sinkPos1, $scope.sinkPos2, $scope.stoneWidth, $scope.stoneDepth, $scope.sinkCount);
        }
    };

    $scope.getSinkWD = function (sinkWidth, sinkDepth) {
        angular.forEach($scope.productData.prepared_attributes, function (value, key) {
            if (key == 'sink_width') {
                angular.forEach(value, function (v, k) {
                    if (v.value == sinkWidth) {
                        $scope.sinksWidth = v.label;
                    }
                });
            }
            if (key == 'sink_depth') {
                angular.forEach(value, function (v, k) {
                    if (v.value == sinkDepth) {
                        $scope.sinksDepth = v.label;
                    }
                });
            }
        });
    };

    $scope.sinkPosition = function (sinkPos1, sinkPos2, stoneWidth, stoneDepth, sinkCount, focus, sinkVertPos1, sinkVertPos2, sink1Hole, sink2Hole) {

        $scope.message = '';
        $scope.resets(stoneReset = 0, edgingReset = 0, sinksReset = 0, faucetsReset = 1);
        if (sinkCount == 0) {
            return;
        }
        if (((sinkPos1 == '' || sinkPos2 == '') && sinkCount == 2) || (sinkPos1 == '' && sinkCount == 1)) {
            return;
        }
        if ($scope.sinkSku != 'LAB-CUT-VES') {
            $scope.sinkMin1    = ((Math.ceil((4 + (parseFloat($scope.sinksWidth) / 2)) * 4) / 4).toFixed(2))/1;
            $scope.sinkMin2    = ((Math.ceil((Number(sinkPos1) + Number($scope.sinksWidth) + 8) * 4) / 4).toFixed(2))/1;
            sinkMax1    = ((Math.floor((Number(stoneWidth) - 4 - (Number($scope.sinksWidth) / 2)) * 4) / 4).toFixed(2))/1;
            $scope.sinkMax2    = ((Math.floor((parseFloat(stoneWidth) - 4 - (parseFloat($scope.sinksWidth) / 2)) * 4) / 4).toFixed(2))/1;
            sinkMax1of2 = ((Math.floor((Number(sinkPos2) - 8 - Number($scope.sinksWidth)) * 4) / 4).toFixed(2))/1;
            if (Number(sinkPos1) == Number($scope.sinkMin1) && sinkPos1 != '' && focus == 'sink1' || Number(sinkPos1) < (Number($scope.sinkMin1) + .25) && sinkPos1 != '' && focus == 'sink1') {
                $scope.message = 'Sink 1 cannot move any further left.  It must be a minimum of 4 inches from the left edge.';
                $scope.sinkPos1 = $scope.sinkMin1;
            }
            if (Number(sinkPos2) == Number($scope.sinkMin2) && sinkPos2 != '' && focus == 'sink2' || Number(sinkPos2) < (Number($scope.sinkMin2) + .25) && sinkPos2 != '' && focus == 'sink2') {
                $scope.message = 'Sink 2 cannot move any further left.  It must be a minimum of 8 inches from sink 1.';
                $scope.sinkPos2 = $scope.sinkMin2;
            }
            if (Number(sinkPos2) == Number($scope.sinkMax2) && sinkPos2 != '' && focus == 'sink2' || Number(sinkPos2) > (Number($scope.sinkMax2) - .25) && sinkPos2 != '' && focus == 'sink2') {
                $scope.message = 'Sink 2 cannot move any further right, it must be a minimum of 4 inches from the right.';
                $scope.sinkPos2 = $scope.sinkMax2;
            }
            if (sinkCount == 2) {
                $scope.sinkMax1 = sinkMax1of2;
                if (Number($scope.sinkPos1) == Number($scope.sinkMax1) && focus == 'sink1' || Number($scope.sinkPos1) > (Number($scope.sinkMax1) - .25) && focus == 'sink1') {
                    $scope.sinkPos1 == $scope.sinkMax1;
                    $scope.message = 'Sink 1 cannot move any further right, it must be a minimum of 8 inches from sink 2.';
                }
            } else if (sinkCount == 1) {
                $scope.sinkMax1 = sinkMax1;
                if (Number($scope.sinkPos1) == Number($scope.sinkMax1) || Number($scope.sinkPos1) > (Number($scope.sinkMax1) - .25)) {
                    $scope.sinkPos1 == $scope.sinkMax1;
                    $scope.message = 'Sink 1 cannot move any further right, it must be a minimum of 4 inches from the right.';
                }
            }


            $scope.setSinkCSS($scope.stoneWidth, $scope.stoneDepth, $scope.sinksWidth, $scope.sinksDepth, $scope.sinksWidth, $scope.sinksDepth, sinkPos1, sinkPos2, Number($scope.stoneDepth) / 2, Number($scope.stoneDepth) / 2);

        } else {

            vertMax = parseFloat($scope.stoneDepth) - 14;
            horiMax = '';
            if(vertMax > 10){
                vertMax = 10;
            }

            $scope.sink1HoleMax = vertMax;
            $scope.sink2HoleMax = vertMax;

            /*if($scope.sinkCount == 1){
                horiMax = parseFloat($scope.stoneWidth) - 14;
                if(horiMax > 10){
                    horiMax = 10;
                }
                if(horiMax <= vertMax){
                    $scope.sink1HoleMax = horiMax;
                }else{
                    $scope.sink1HoleMax = vertMax;
                }
            }
            if($scope.sinkCount == 2){
                horiMax1 = parseFloat($scope.sinkPos2) - (parseFloat($scope.sink2Hole)/2) - 15;
                if(horiMax1 > 10){
                    horiMax1 = 10;
                }
                if(horiMax1 <= vertMax){
                    $scope.sink1HoleMax = horiMax1;
                }else{
                    $scope.sink1HoleMax = vertMax;
                }
                horiMax2 = parseFloat($scope.stoneWidth) - (parseFloat($scope.sinkPos1) + (parseFloat($scope.sink1Hole)/2) + 15);
                if(horiMax2 > 10){
                    horiMax2 = 10;
                }
                if(horiMax2 <= vertMax){
                    $scope.sink2HoleMax = horiMax2;
                }else{
                    $scope.sink2HoleMax = vertMax;
                }
            }*/

            vesselSinkMin1    = ((Math.ceil((7 + (Number($scope.sink1Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselSinkMin2    = ((Math.ceil((Number(sinkPos1) + (Number($scope.sink1Hole) / 2) + 8 + (Number($scope.sink2Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselSinkMax1    = ((Math.floor((Number($scope.stoneWidth) - 14 - Number($scope.sink2Hole) - (Number($scope.sink1Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselSinkMax2    = ((Math.floor((Number($scope.stoneWidth) - 7 - (Number($scope.sink2Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselSinkMax1of2 = ((Math.floor((Number($scope.sinkPos2) - (Number($scope.sink2Hole) / 2) - 8 - (Number($scope.sink1Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselBackMin1    = ((Math.ceil((7 + (Number($scope.sink1Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselBackMin2    = ((Math.ceil((7 + (Number($scope.sink2Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselBackMax1    = ((Math.floor((Number($scope.stoneDepth) - 7 - (Number($scope.sink1Hole) / 2)) * 4) / 4).toFixed(2))/1;;
            vesselBackMax2    = ((Math.floor((Number($scope.stoneDepth) - 7 - (Number($scope.sink2Hole) / 2)) * 4) / 4).toFixed(2))/1;;

            $scope.sinkMin1 = vesselSinkMin1;
            $scope.sinkMin2 = vesselSinkMin2;
            $scope.sinkMax1 = vesselSinkMax1;
            $scope.sinkMax2 = vesselSinkMax2;
            $scope.sink1MinDepth = vesselBackMin1;
            $scope.sink1MaxDepth = vesselBackMax1;
            $scope.sink2MinDepth = vesselBackMin2;
            $scope.sink2MaxDepth = vesselBackMax2;

			$scope.faucet1Option = 'vn1';
			$scope.faucet2Option = 'vn2';
			$scope.faucetCenter1 = (Number($scope.sink1Hole)/2)+1;
			$scope.faucetCenter2 = (Number($scope.sink2Hole)/2)+1;
			$scope.f1o = 8;
			$scope.f2o = 4;

            if (Number(sinkPos1) == Number(vesselSinkMin1) && sinkPos1 != '' && focus == 'sink1' || Number(sinkPos1) < (Number(vesselSinkMin1) + .25) && sinkPos1 != '' && (focus == 'sink1' || focus == 'sink1Hole')) {
                $scope.message = 'Vessel Sink 1 cannot move any further, it must be a minimum of 7 inches from the left.';
                $scope.sinkPos1 = $scope.sinkMin1;
            }
            if (Number(sinkPos2) == Number(vesselSinkMin2) && sinkPos2 != '' && focus == 'sink2' || Number(sinkPos2) < (Number(vesselSinkMin2) + .25) && sinkPos2 != '' && (focus == 'sink2' || focus == 'sink2Hole')) {
                $scope.message = 'Vessel Sink 2 cannot move any further left, it must be a minimum of 8 inches from sink 1';
                $scope.sinkPos2 = $scope.sinkMin2;
            }
            if (Number(sinkPos2) == Number(vesselSinkMax2) && sinkPos2 != '' && focus == 'sink2' || Number(sinkPos2) > (Number(vesselSinkMax2) - .25) && sinkPos2 != '' && (focus == 'sink2' || focus == 'sink2Hole')) {
                $scope.message = 'Vessel Sink 2 cannot move any further right, it must be a minimum of 7 inches from the right.';
                $scope.sinkPos2 = $scope.sinkMax2;
            }
            if (sinkCount == 2) {
                $scope.sinkMax1 = vesselSinkMax1of2;
                if (Number(sinkPos1) == Number(vesselSinkMax1of2) && focus == 'sink1' || Number(sinkPos1) > (Number(vesselSinkMax1of2) - .25) && (focus == 'sink1' || focus == 'sink1Hole')) {
                    $scope.message = 'Vessel Sink 1 cannot move any further right, it must be a minimum of 8 inches from sink 2.';
                    $scope.sinkPos1 = $scope.sinkMax1;
                }
            } else if (sinkCount == 1) {
                $scope.sinkMax1 = vesselSinkMax1;
                if (Number(sinkPos1) == Number(vesselSinkMax1) || Number(sinkPos1) > (Number(vesselSinkMax1) - .25)) {
                    $scope.message = 'Vessel Sink 1 cannot move any further right, it must be a minimum of 7 inches from the right.';
                    $scope.sinkPos1 = $scope.sinkMax1;
                }
            }
            if($scope.sinkVertPos1 <= vesselBackMin1){
                $scope.sinkVertPos1 = vesselBackMin1;
            }
            if($scope.sinkVertPos1 >= vesselBackMax1){
                $scope.sinkVertPos1 = vesselBackMax1;
            }
            if($scope.sinkVertPos2 <= vesselBackMin2){
                $scope.sinkVertPos2 = vesselBackMin2;
            }
            if($scope.sinkVertPos2 >= vesselBackMax2){
                $scope.sinkVertPos2 = vesselBackMax2;
            }
            if (sinkVertPos1 == vesselBackMin1 && focus == 'sink1vert' || sinkVertPos1 < (vesselBackMin1 + .25) && focus == 'sink1vert') {
                $scope.message = 'Vessel Sink 1 must be a minimum of 7" from the back side.';
            }
            if (sinkVertPos2 == vesselBackMin2 && focus == 'sink2vert' || sinkVertPos2 < (vesselBackMin2 + .25) && focus == 'sink2vert') {
                $scope.message = 'Vessel Sink 2 must be a minimum of 7" from the back side.';
            }
            if (sinkVertPos1 == vesselBackMax1 && focus == 'sink1vert' || sinkVertPos2 > (vesselBackMax1 - .25) && focus == 'sink1vert') {
                $scope.message = 'Vessel Sink 1 must be a minimum of 7" from the front side.';
            }
            if (sinkVertPos2 == vesselBackMax2 && focus == 'sink2vert' || sinkVertPos2 > (vesselBackMax2 - .25) && focus == 'sink2vert') {
                $scope.message = 'Vessel Sink 2 must be a minimum of 7" from the front side.';
            }

            $scope.selectedSink1Width = $scope.sink1Hole;
            $scope.selectedSink1Depth = $scope.sink1Hole;
            $scope.selectedSink2Width = $scope.sink2Hole;
            $scope.selectedSink2Depth = $scope.sink2Hole;

            $scope.setSinkCSS(stoneWidth, stoneDepth, $scope.sink1Hole, $scope.sink1Hole, $scope.sink2Hole, $scope.sink2Hole, $scope.sinkPos1, $scope.sinkPos2, $scope.sinkVertPos1, $scope.sinkVertPos2);
        }
    };

    $scope.setSinkCSS      = function (stoneWidth, stoneDepth, sink1Width, sink1Depth, sink2Width, sink2Depth, sinkPos1, sinkPos2, sinkDPos1, sinkDPos2) {
        // 8 is the inches to CSS multiplier.  1 inches = 8 pixels.
        $scope.sink1leftperc      = Number(sinkPos1) / Number(stoneWidth);
        $scope.sink2leftperc      = Number(sinkPos2) / Number(stoneWidth);
        $scope.sink1halfwidth     = Number(sink1Width) / 2;
        $scope.sink1halfwidthperc = $scope.sink1halfwidth / Number(stoneWidth);
        $scope.sink2halfwidth     = Number(sink2Width) / 2;
        $scope.sink2halfwidthperc = $scope.sink2halfwidth / Number(stoneWidth);

        $scope.sink1CSSLeft = ((Number(stoneWidth) * 8) * ($scope.sink1leftperc - $scope.sink1halfwidthperc)) + 'px';
        $scope.sink2CSSLeft = ((Number(stoneWidth) * 8) * ($scope.sink2leftperc - $scope.sink2halfwidthperc)) + 'px';

        $scope.sink1CSSTop = (Number(sinkDPos1) * 8) - ((Number(sink1Depth) * 8) / 2) + 'px';
        $scope.sink2CSSTop = (Number(sinkDPos2) * 8) - ((Number(sink2Depth) * 8) / 2) + 'px';

        if ($scope.sinkSku != 'BEA-SIN-RL' && $scope.sinkSku != 'BEA-SIN-RS') {
            $scope.sinkRadius1 = (Number($scope.sink1halfwidth) * 8) + 'px / ' + ((Number(sink1Depth) / 2) * 8) + 'px';
            $scope.sinkRadius2 = (Number($scope.sink2halfwidth) * 8) + 'px / ' + ((Number(sink2Depth) / 2) * 8) + 'px';
        } else {
            $scope.sinkRadius1 = 0;
            $scope.sinkRadius2 = 0;
        }

        $scope.sinkmeasure1top   = (Number(sinkDPos1) * 8) + ((Number(sink1Depth) / 2) * 8) + 'px';
        $scope.sinkmeasure1width = ((Number(stoneWidth) * 8) * ($scope.sink1leftperc - $scope.sink1halfwidthperc)) + ((Number(sink1Width) / 2) * 8) + 'px';

        $scope.sinkmeasure2top   = (Number(sinkDPos2) * 8) + ((Number(sink2Depth) / 2) * 8) + 'px';
        $scope.sinkmeasure2width = ((Number(stoneWidth) * 8) * ($scope.sink2leftperc - $scope.sink2halfwidthperc)) + ((Number(sink2Width) / 2) * 8) + 'px';
    };
    $scope.processSink     = function () {
        if ($scope.sinkSku == 'LAB-CUT-VES') {
            if ($scope.selectedSink1Width == 1.75) {
                sink1Price = $scope.selectedSink.price;
            } else {
                sink1Price = $scope.selectedSink.custom_vessel_price;
            }
            if ($scope.sinkCount == 2) {
                if ($scope.selectedSink2Width == 1.75) {
                    sink2Price = $scope.selectedSink.price;
                } else {
                    sink2Price = $scope.selectedSink.custom_vessel_price;
                }
            }
        } else {
            if ($scope.selectedSink.special_price != null) {
                sink1Price = $scope.selectedSink.special_price;
                if ($scope.sinkCount == 2) {
                    sink2Price = $scope.selectedSink.special_price;
                }
            } else {
                sink1Price = $scope.selectedSink.price;
                if ($scope.sinkCount == 2) {
                    sink2Price = $scope.selectedSink.price;
                }
            }
        }


        if ($scope.sinkCount > 0) {
            $parse('price.sink1').assign($scope);
            $scope.price.sink1 = sink1Price;
            if($scope.price.sink2){
                delete $scope.price.sink2;
            }
        }
        if ($scope.sinkCount == 2) {
            $parse('price.sink2').assign($scope);
            $scope.price.sink2 = sink2Price;
        }
        $scope.proceed('faucetsTab');
    };
    $scope.setFaucets      = function () {
        // 8 is the inches to CSS multiplier.  1 inches = 8 pixels.
        if ($scope.sinkSku == 'LAB-CUT-VES') {
            $scope.faucet45PixelDistance1 = (Math.sin(45 / 180 * Math.PI) * Number($scope.faucetCenter1)) * 8;
            $scope.faucet45PixelDistance2 = (Math.sin(45 / 180 * Math.PI) * Number($scope.faucetCenter2)) * 8;

			$scope.cssFaucetCenter1 = Number($scope.faucetCenter1)*8;
			$scope.cssFaucetCenter2 = Number($scope.faucetCenter2)*8;

			$scope.faucet22bottom  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucet45PixelDistance2)))+'px';
			$scope.faucet22right = ((Number($scope.sinkPos2) * 8) - Number($scope.faucet45PixelDistance2))+'px';


            if ($scope.faucet1Option == 'vl1') {
                // 11 = faucet hole size 11px
                $scope.faucet12top  = ((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucet45PixelDistance1))) - 11 + 'px';
                $scope.faucet12left = (((Number($scope.sinkPos1) * 8) - Number($scope.faucet45PixelDistance1)) - 11) + 'px';
				$scope.faucet12bottom  = ((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucet45PixelDistance1)))+'px';
				$scope.faucet12right = ((Number($scope.sinkPos1) * 8) - Number($scope.faucet45PixelDistance1))+'px';
				$scope.faucet1Rotate = 'rotate(-45deg)';
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet1MinHD = (Number($scope.sink1Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet1MaxHD = (Number($scope.sinkVertPos1)-4)/.707;
                $scope.faucet1Label = "Vessel Sink - Faucet Left Hole";

            }
            if ($scope.faucet1Option == 'vc1') {
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet1MinHD = (Number($scope.sink1Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet1MaxHD = Number($scope.sinkVertPos1)-4;
				if($scope.faucet1MaxHD<$scope.faucetCenter1){
					$scope.faucetCenter1 = $scope.faucet1MaxHD;
					$scope.cssFaucetCenter1 = Number($scope.faucetCenter1)*8;
				}
                $scope.faucet12top  = (((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucetCenter1)*8)) - 11) + 'px';
                $scope.faucet12left = ((Number($scope.sinkPos1) * 8) - 5.5) + 'px';
				$scope.faucet12bottom  = ((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucetCenter1)*8)) + 'px';
				$scope.faucet12right = (Number($scope.sinkPos1) * 8)+'px';
				$scope.faucet1Rotate = 'rotate(0)';
                $scope.faucet1Label = "Vessel Sink - Faucet Center Hole";

            }
            if ($scope.faucet1Option == 'vr1') {
                $scope.faucet12top  = ((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucet45PixelDistance1))) - 11 + 'px';
                $scope.faucet12left = ((Number($scope.sinkPos1) * 8) + Number($scope.faucet45PixelDistance1)) + 'px';
				$scope.faucet12bottom  = ((Number($scope.sinkVertPos1) * 8) - (Number($scope.faucet45PixelDistance1))) + 'px';
				$scope.faucet12right = ((Number($scope.sinkPos1) * 8) + Number($scope.faucet45PixelDistance1)) + 'px';
				$scope.faucet1Rotate = 'rotate(45deg)';
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet1MinHD = (Number($scope.sink1Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet1MaxHD = (Number($scope.sinkVertPos1)-4)/.707;
                $scope.faucet1Label = "Vessel Sink - Faucet Right Hole";
            }
            if ($scope.faucet2Option == 'vl2') {
                $scope.faucet22top  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucet45PixelDistance2))) - 11 + 'px';
                $scope.faucet22left = (((Number($scope.sinkPos2) * 8) - Number($scope.faucet45PixelDistance2)) - 11) + 'px';
				$scope.faucet22bottom  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucet45PixelDistance2)))+'px';
				$scope.faucet22right = ((Number($scope.sinkPos2) * 8) - Number($scope.faucet45PixelDistance2))+'px';
				$scope.faucet2Rotate = 'rotate(-45deg)';
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet2MinHD = (Number($scope.sink2Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet2MaxHD = (Number($scope.sinkVertPos2)-4)/.707;
                $scope.faucet2Label = "Vessel Sink - Faucet Left Hole";
            }
            if ($scope.faucet2Option == 'vc2') {
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet2MinHD = (Number($scope.sink2Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet2MaxHD = Number($scope.sinkVertPos2)-4;
				if($scope.faucet2MaxHD<$scope.faucetCenter2){
					$scope.faucetCenter2 = $scope.faucet2MaxHD;
					$scope.cssFaucetCenter2 = Number($scope.faucetCenter2)*8;
				}
                $scope.faucet22top  = (((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucetCenter2)*8)) - 11) + 'px';
                $scope.faucet22left = ((Number($scope.sinkPos2) * 8) - 5.5) + 'px';
				$scope.faucet22bottom  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucetCenter2)*8)) + 'px';
				$scope.faucet22right = (Number($scope.sinkPos2) * 8)+'px';
				$scope.faucet2Rotate = 'rotate(0)';
                $scope.faucet2Label = "Vessel Sink - Faucet Center Hole";
            }
            if ($scope.faucet2Option == 'vr2') {
                $scope.faucet22top  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucet45PixelDistance2))) - 11 + 'px';
                $scope.faucet22left = ((Number($scope.sinkPos2) * 8) + Number($scope.faucet45PixelDistance2)) + 'px';
				$scope.faucet22bottom  = ((Number($scope.sinkVertPos2) * 8) - (Number($scope.faucet45PixelDistance2))) + 'px';
				$scope.faucet22right = ((Number($scope.sinkPos2) * 8) + Number($scope.faucet45PixelDistance2)) + 'px';
				$scope.faucet2Rotate = 'rotate(45deg)';
				//minimum distance for faucet hole from sink hole is 4.5 inches
				$scope.faucet2MinHD = (Number($scope.sink2Hole)/2)+1;
				// faucet hole minimum 4 inches from the edge
				$scope.faucet2MaxHD = (Number($scope.sinkVertPos2)-4)/.707;
                $scope.faucet2Label = "Vessel Sink - Faucet Right Hole";
            }
        } else {
            // 17 = 6 pixel between sink and faucet hole, plus 11 pixel wide faucet hole (CSS anchor is top left)
            // 5.5 = half of 11px, center faucet hole with center of sink.
            if($scope.faucet1Option == 0){
                $scope.faucet1Label = "No Faucet Hole";
            }

            if ($scope.faucet1Option > 0) {
                $scope.faucet1Label = "1 Faucet Hole";
                $scope.faucet12top  = ((Number($scope.sinkVertPos1) * 8) - ((Number($scope.selectedSink1Depth) * 8) / 2) - 17) + 'px';
                $scope.faucet12left = ((Number($scope.sinkPos1) * 8) - 5.5) + 'px';

                $scope.faucet22top  = ((Number($scope.sinkVertPos2) * 8) - ((Number($scope.selectedSink2Depth) * 8) / 2) - 17) + 'px';
                $scope.faucet22left = ((Number($scope.sinkPos2) * 8) - 5.5) + 'px';

            }
            if ($scope.faucet1Option == '2') {
                $scope.faucet1Label = "3 Faucet Holes (narrow)";
                if($scope.sinkSku == 'BEA-SIN-RL' || $scope.sinkSku == 'BEA-SIN-RS'){
                    $scope.faucet11top  = $scope.faucet12top;
                    // side faucets 8 pixels away from middle faucet (8px + 11px hole width)
                    $scope.faucet11left = (parseInt($scope.faucet12left) - 19) + 'px';
                    $scope.faucet13top  = $scope.faucet12top;
                    $scope.faucet13left = (parseInt($scope.faucet12left) + 19) + 'px';

                    $scope.faucet21top  = $scope.faucet22top;
                    $scope.faucet21left = (parseInt($scope.faucet22left) - 19) + 'px';
                    $scope.faucet23top  = $scope.faucet22top;
                    $scope.faucet23left = (parseInt($scope.faucet22left) + 19) + 'px';
                }else if($scope.sinkSku == 'BEA-SIN-OL' || $scope.sinkSku == 'BEA-SIN-OS'){
                    // side faucets 5 pixels lower than middle faucet
                    $scope.faucet11top  = (parseInt($scope.faucet12top) + 5) + 'px';
                    // side faucets 8 pixels away from middle faucet (8px + 11px hole width)
                    $scope.faucet11left = (parseInt($scope.faucet12left) - 19) + 'px';
                    $scope.faucet13top  = (parseInt($scope.faucet12top) + 5) + 'px';
                    $scope.faucet13left = (parseInt($scope.faucet12left) + 19) + 'px';

                    $scope.faucet21top  = (parseInt($scope.faucet22top) + 5) + 'px';
                    $scope.faucet21left = (parseInt($scope.faucet22left) - 19) + 'px';
                    $scope.faucet23top  = (parseInt($scope.faucet22top) + 5) + 'px';
                    $scope.faucet23left = (parseInt($scope.faucet22left) + 19) + 'px';
                }
            }
            if ($scope.faucet1Option == '3') {
                $scope.faucet1Label = "3 Faucet Holes (wide)";
                if($scope.sinkSku == 'BEA-SIN-RL' || $scope.sinkSku == 'BEA-SIN-RS'){
                    $scope.faucet11top  = $scope.faucet12top;
                    // side faucets 18 pixels away from middle faucet (18px + 11px hole width)
                    $scope.faucet11left = (parseInt($scope.faucet12left) - 29) + 'px';
                    $scope.faucet13top  = $scope.faucet12top;
                    $scope.faucet13left = (parseInt($scope.faucet12left) + 29) + 'px';

                    $scope.faucet21top  = $scope.faucet22top;
                    $scope.faucet21left = (parseInt($scope.faucet22left) - 29) + 'px';
                    $scope.faucet23top  = $scope.faucet22top;
                    $scope.faucet23left = (parseInt($scope.faucet22left) + 29) + 'px';
                }else if($scope.sinkSku == 'BEA-SIN-OL' || $scope.sinkSku == 'BEA-SIN-OS'){
                    $scope.faucet11top  = (parseInt($scope.faucet12top) + 5) + 'px';
                    // side faucets 18 pixels away from middle faucet (18px + 11px hole width)
                    $scope.faucet11left = (parseInt($scope.faucet12left) - 29) + 'px';
                    $scope.faucet13top  = (parseInt($scope.faucet12top) + 5) + 'px';
                    $scope.faucet13left = (parseInt($scope.faucet12left) + 29) + 'px';

                    $scope.faucet21top  = (parseInt($scope.faucet22top) + 5) + 'px';
                    $scope.faucet21left = (parseInt($scope.faucet22left) - 29) + 'px';
                    $scope.faucet23top  = (parseInt($scope.faucet22top) + 5) + 'px';
                    $scope.faucet23left = (parseInt($scope.faucet22left) + 29) + 'px';
                }

            }
            if ($scope.sinkCount == 2) {
                $scope.faucet2Option = $scope.faucet1Option;
                $scope.faucet2Label = $scope.faucet1Label;
            }
        }
    };
    $scope.processFaucets  = function () {
        faucet1Price = 0;
        faucet2Price = 0;
        if ($scope.faucet1Option != 0 && $scope.faucet1Option != 2 && $scope.faucet1Option != 3) {
            if ($scope.faucet1Option != 'vn1') {
                angular.forEach($scope.productsArray, function (value) {
                    if (value.sku == 'LAB-CUT-S') {
                        $scope.faucet1Id = value.entity_id;
                        if (value.special_price != null) {
                            faucet1Price = value.special_price;
                        } else {
                            faucet1Price = value.price;
                        }
                    }
                });
            }
            if ($scope.sinkCount > 1 && $scope.faucet2Option != 'vn2') {
                angular.forEach($scope.productsArray, function (value) {
                    if (value.sku == 'LAB-CUT-S') {
                        $scope.faucet2Id = value.entity_id;
                        if (value.special_price != null) {
                            faucet2Price = value.special_price;
                        } else {
                            faucet2Price = value.price;
                        }
                    }
                });
            }
        }
        if ($scope.faucet1Option == 2) {
            angular.forEach($scope.productsArray, function (value) {
                if (value.sku == 'LAB-CUT-4') {
                    $scope.faucet1Id = value.entity_id;
                    if ($scope.sinkCount > 1) {
                        $scope.faucet2Id = value.entity_id;
                    }
                    if (value.special_price != null) {
                        faucet1Price = value.special_price;
                        if ($scope.sinkCount > 1) {
                            faucet2Price = value.special_price;
                        }
                    } else {
                        faucet1Price = value.price;
                        if ($scope.sinkCount > 1) {
                            faucet2Price = value.price;
                        }
                    }
                }
            });
        }
        if ($scope.faucet1Option == 3) {
            angular.forEach($scope.productsArray, function (value) {
                if (value.sku == 'LAB-CUT-8') {
                    faucet1Price = value.special_price;
                    $scope.faucet1Id = value.entity_id;
                    if ($scope.sinkCount > 1) {
                        $scope.faucet2Id = value.entity_id;
                    }
                    if (value.special_price != null) {

                        if ($scope.sinkCount > 1) {
                            faucet2Price = value.special_price;
                        }
                    } else {
                        faucet1Price = value.price;
                        if ($scope.sinkCount > 1) {
                            faucet2Price = value.price;
                        }
                    }
                }
            });
        }
        if ($scope.sinkCount > 0) {
            $parse('price.faucet1').assign($scope);
            $scope.price.faucet1 = faucet1Price;
            if($scope.price.faucet2){
                delete $scope.price.faucet2;
            }
        }
        if ($scope.sinkCount > 1) {
            $parse('price.faucet2').assign($scope);
            $scope.price.faucet2 = faucet2Price;
        }
        $scope.calculatePrices();
        $scope.proceed('priceTab');
    }
    $scope.calculatePrices = function () {
        $scope.totalPrice = 0;
        angular.forEach($scope.price, function (value) {
            if(value != '') {
                $scope.totalPrice += parseFloat(value);
            }
        });
        $scope.diagramId = Date.now() + Math.floor(Math.random() * 99) + 1;
    };
    $scope.resets = function(stoneReset, edgingReset, sinksReset, faucetsReset){
        if(stoneReset == 1){
            $scope.slabName = '';
            $scope.slabBg = '';
            $scope.slabSelected = -1;
            $scope.slabProductId = '';
            $scope.remnantFlag = '';
            $parse('price.slab').assign($scope);
            $scope.price.slab = '';
        }
        if(edgingReset == 1){
            $scope.upsells = '';
            $scope.upsellIds = '';
            $scope.upsellArray = '';
            $scope.totalPolish = '';
            $scope.totalBacksplash = '';
            $scope.backsplashSku = '';
            $scope.backsplashId = '';
            $parse('backsplash.left').assign($scope);
            $parse('backsplash.right').assign($scope);
            $parse('backsplash.back').assign($scope);
            $parse('polish.left').assign($scope);
            $parse('polish.right').assign($scope);
            $parse('polish.back').assign($scope);
            $scope.backsplash.left = '';
            $scope.backsplash.right = '';
            $scope.backsplash.back = '';
            $scope.polish.left = '';
            $scope.polish.right = '';
            $scope.polish.back = '';
            $scope.edge = '';
            $scope.edgeSelected = -1;
            $scope.edgeType = '';
            $scope.price.polish = '';
            $scope.price.backsplash = '';
        }
        if(sinksReset == 1){
            $scope.sinkSku = '';
            $scope.sinkName = '';
            $scope.sinkImg = '';
            $scope.sinkSelected = -1;
            $scope.selectedSink = '';
            $scope.sinkCount = '';
            $scope.sinkPos1 = '';
            $scope.sinkPos2 = '';
            $scope.sinkVertPos1 = '';
            $scope.sinkVertPos2 = '';
            $scope.selectedSink1Width = '';
            $scope.selectedSink1Depth = '';
            $scope.selectedSink2Width = '';
            $scope.selectedSink2Depth = '';
            $scope.sinksWidth = '';
            $scope.sinksDepth = '';
            $scope.sinkMin1 = '';
            $scope.sinkMin2 = '';
            $scope.sinkMax1 = '';
            $scope.sinkMax2 = '';
            $scope.sink1Hole = '';
            $scope.sink2Hole = '';
            $scope.sink1MinDepth = '';
            $scope.sink1MaxDepth = '';
            $scope.sink2MinDepth = '';
            $scope.sink1MaxDepth = '';
            $scope.price.sink1 = '';
            $scope.price.sink2 = '';
            $scope.sink1leftperc = '';
            $scope.sink2leftperc = '';
            $scope.sink1halfwidth = '';
            $scope.sink2halfwidth = '';
            $scope.sink1halfwidthperc = '';
            $scope.sink2halfwidthperc = '';
            $scope.sink1CSSLeft = '';
            $scope.sink2CSSLeft = '';
            $scope.sink1CSSTop = '';
            $scope.sink2CSSTop = '';
            $scope.sinkRadius1 = '';
            $scope.sinkRadius2 = '';
            $scope.sinkmeasure1top = '';
            $scope.sinkmeasure1width = '';
            $scope.sinkmeasure2top = '';
            $scope.sinkmeasure2width = '';
        }
        if(faucetsReset == 1){
            $scope.faucet1Option = '';
            $scope.fauceet2Option = '';
            $scope.faucetCenter1 = '';
            $scope.faucetCenter2 = '';
            $scope.f1o = '';
            $scope.f2o = '';
            if($scope.sinkCount==0) {
                $scope.faucet1Option = '0';
                $scope.f1o = '1';
            }else{
                $scope.faucet1Option = '';
                $scope.faucet2Option = '';
                $scope.f1o = '';
                $scope.f2o = '';
            }
            $scope.faucet1Id = '';
            $scope.faucet2Id = '';
            $scope.faucet1Rotate = '';
            $scope.faucet2Rotate = '';
            $scope.faucet1MinHD = '';
            $scope.faucet2MinHD = '';
            $scope.faucet1MaxHD = '';
            $scope.faucet2MaxHD = '';
            $scope.faucetCenter1 = '';
            $scope.faucetCenter2 = '';
            $scope.faucet45pixelDistance1 = '';
            $scope.faucet45pixelDistance2 = '';
            $scope.cssFaucetCenter1 = '';
            $scope.cssFaucetCenter2 = '';
            $scope.faucet11top = '';
            $scope.faucet11left = '';
            $scope.faucet12top = '';
            $scope.faucet12left = '';
            $scope.faucet12bottom = '';
            $scope.faucet12right = '';
            $scope.faucet13top = '';
            $scope.faucet13left = '';
            $scope.faucet21top = '';
            $scope.faucet21left = '';
            $scope.faucet22top = '';
            $scope.faucet22left = '';
            $scope.faucet22bottom = '';
            $scope.faucet22right = '';
            $scope.faucet23top = '';
            $scope.faucet23left = '';
            $scope.price.faucet1 = '';
            $scope.price.faucet2 = '';
        }
        $scope.totalPrice = '';
    }
}]);
builder.filter('underscoreless', function () {
    return function (input) {
        return input.replace(/_/g, ' ');
    };
});
builder.filter('zeroEvaluate', function () {
    return function (item, scope, filterKey, index) {
        var i = 0;
        angular.forEach(scope.$parent.$parent.filteredCollection, function(val, key){
            if(scope.dimensions(scope.stoneWidth, scope.stoneDepth, val)){
                paRef = scope.productData.prepared_attributes[filterKey][index];
                valRef = val[filterKey];
                if(valRef == paRef.value){
                    i = i + 1;
                }
            }
        });
        if(i>0){
            return true;
        }else{
            return false;
        }
    };
});
builder.directive('stoneInit', function(){
    return {
        restrict: 'A',
        scope: {model: '=ngModel'},
        link: function(scope, elem, attr){
            scope.$watch('model', function(v){
                scope.$parent.resets(stoneReset = 1, edgingReset = 1, sinksReset = 1, faucetsReset = 1);
            });
        }
    }
});
builder.directive('slabWidth', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="width">{{stoneWidth}}"</span>'

    }
});
builder.directive('slabDepth', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="depth">{{stoneDepth}}"</span>'
    }
});
builder.directive('thickness', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="thickness">{{thicknessLabel}}</span>'
    }
});
builder.directive('slabName', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="slab-name">{{slabName}}</span>'
    }
});
builder.directive('edge', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<a> {{edge.short_description}}</a>'
    }
});
builder.directive('sinkName', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="sink-name">{{sinkName}}</span>'
    }
});
builder.directive('sink1left', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<div class="sink-1-container"><span class="sink sink-1 {{sinkSku}}" ng-style="{left: sink1CSSLeft, top: sink1CSSTop, width: selectedSink1Width*8, height: selectedSink1Depth*8, \'border-radius\': sinkRadius1}"><img ng-src="/media/catalog/product{{sinkImg}}"/></span><div class="sink-measure-1 sink-measure" ng-style="{top: sinkmeasure1top, width: sinkmeasure1width}" ng-show="sinkmeasure1width"><span>{{sinkPos1}}"</span></div></div>'
    }
});
builder.directive('sink2left', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<div class="sink-2-container"><span class="sink sink-2 {{sinkSku}}" ng-style="{left: sink2CSSLeft, top: sink2CSSTop, width: selectedSink2Width*8, height: selectedSink2Depth*8, \'border-radius\': sinkRadius2}"><img ng-src="/media/catalog/product{{sinkImg}}"/></span><div class="sink-measure-2 sink-measure" ng-style="{top: sinkmeasure2top, width: sinkmeasure2width}"><span>{{sinkPos2}}"</span></div></div>'
    }
});
builder.directive('faucet1.1', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-hole faucet-1-1" ng-style="{top: faucet11top, left: faucet11left}" ng-show="faucet1Option>1"></span>'
    }
});
builder.directive('faucet1.2', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-wrap" ng-show="faucet1Option!=0&&faucet1Option!=\'vn1\'&&faucet1Option!=\'\'"><span class="faucet-hole faucet-1-2" ng-style="{top: faucet12top, left: faucet12left}"></span><span class="faucet-line" ng-style="{top: faucet12bottom, left: faucet12right, height: cssFaucetCenter1, transform: faucet1Rotate}" ng-show="sinkSku == \'LAB-CUT-VES\'">{{faucetCenter1}}"</span></span>'
    }
});
builder.directive('faucet1.3', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-hole faucet-1-3" ng-style="{top: faucet13top, left: faucet13left}" ng-show="faucet1Option>1"></span>'
    }
});
builder.directive('faucet2.1', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-hole faucet-2-1" ng-style="{top: faucet21top, left: faucet21left}" ng-show="faucet1Option>1"></span>'
    }
});
builder.directive('faucet2.2', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-wrap" ng-show="faucet1Option!=0&&faucet2Option!=\'vn2\'&&faucet2Option!=\'\'"><span class="faucet-hole faucet-2-2" ng-style="{top: faucet22top, left: faucet22left}"></span><span class="faucet-line" ng-style="{top: faucet22bottom, left: faucet22right, height: cssFaucetCenter2, transform: faucet2Rotate}" ng-show="sinkSku == \'LAB-CUT-VES\'">{{faucetCenter2}}"</span></span>'
    }
});
builder.directive('faucet2.3', function () {
    return {
        restrict: 'E',
        replace:  'true',
        template: '<span class="faucet-hole faucet-2-3" ng-style="{top: faucet23top, left: faucet23left}" ng-show="faucet1Option>1"></span>'
    }
});
builder.filter('spaceless'), function() {
    return function(string) {
        if(!angular.isString(string)){
            return string;
        }
        string.toLowerCase();
        return string.replace(' ', '');
    }
}
// Controller for compiling diagram on completed orders
builder.controller('diagramController', ['$scope', '$http', '$parse', function ($scope, $http, $parse) {
    $scope.diagramLoaded = false;
    $scope.loading = false;

    $scope.loadDiagramData = function (id) {
        $scope.loading = true;
        $http({
            method: 'GET',
            url: getUrl()+'customcounter/index/getdiagram/id/'+id
        }).then(
            function successCallback(response) {
                $scope.loading = false;
                //localStorage.setItem('allProducts', response)
                $scope.diagramData = angular.fromJson(response);
                //stone data
                $scope.stoneWidth = $scope.diagramData.data.raw_data.width;
                $scope.stoneDepth = $scope.diagramData.data.raw_data.depth;
                $scope.thicknessLabel = $scope.diagramData.data.raw_data.thicknessLabel;
                $scope.slabName = $scope.diagramData.data.raw_data.slabName;
                $scope.slabBg = $scope.diagramData.data.raw_data.slabBg;
                //sink data
                $scope.sinkName = $scope.diagramData.data.raw_data.sinkName
                $scope.sinkPos1 = $scope.diagramData.data.raw_data.sinks.sink1Left;
                $scope.sinkPos2 = $scope.diagramData.data.raw_data.sinks.sink2Left;
                $scope.sinkVertPos1 = $scope.diagramData.data.raw_data.sinks.sink1Back;
                $scope.sinkVertPos2 = $scope.diagramData.data.raw_data.sinks.sink2Back;
                //edge data
                $parse('edge.short_description').assign($scope);
                $scope.edge.short_description = $scope.diagramData.data.raw_data.edgeName;
                $parse('polish.left').assign($scope);
                $scope.polish.left = $scope.diagramData.data.raw_data.polish.sideLeft;
                $parse('polish.right').assign($scope);
                $scope.polish.right = $scope.diagramData.data.raw_data.polish.sideRight;
                $parse('polish.back').assign($scope);
                $scope.polish.back = $scope.diagramData.data.raw_data.polish.sideBack;
                $parse('backsplash.left').assign($scope);
                $scope.backsplash.left = $scope.diagramData.data.raw_data.backsplash.sideLeft;
                $parse('backsplash.right').assign($scope);
                $scope.backsplash.right = $scope.diagramData.data.raw_data.backsplash.sideRight;
                $parse('backsplash.back').assign($scope);
                $scope.backsplash.back = $scope.diagramData.data.raw_data.backsplash.sideBack;
                //sink data
                $scope.sinkCount = $scope.diagramData.data.sink_count;
                $scope.sinkSku = $scope.diagramData.data.raw_data.sinks.sinkSku;
                $scope.sinkPos1 = $scope.diagramData.data.raw_data.sinks.sink1Left;
                $scope.sinkPos2 = $scope.diagramData.data.raw_data.sinks.sink2Left;
                $scope.sink1CSSLeft = $scope.diagramData.data.raw_data.sinks.css.sink1CSSLeft;
                $scope.sink1CSSTop = $scope.diagramData.data.raw_data.sinks.css.sink1CSSTop;
                $scope.selectedSink1Width = $scope.diagramData.data.raw_data.sinks.css.selectedSink1Width;
                $scope.selectedSink1Depth = $scope.diagramData.data.raw_data.sinks.css.selectedSink1Depth;
                $scope.sinkRadius1 = $scope.diagramData.data.raw_data.sinks.css.sinkRadius1;
                $scope.sinkmeasure1top = $scope.diagramData.data.raw_data.sinks.css.sinkmeasure1top;
                $scope.sinkmeasure1width = $scope.diagramData.data.raw_data.sinks.css.sinkmeasure1width;
                $scope.sink2CSSLeft = $scope.diagramData.data.raw_data.sinks.css.sink2CSSLeft;
                $scope.sink2CSSTop = $scope.diagramData.data.raw_data.sinks.css.sink2CSSTop;
                $scope.selectedSink2Width = $scope.diagramData.data.raw_data.sinks.css.selectedSink2Width;
                $scope.selectedSink2Depth = $scope.diagramData.data.raw_data.sinks.css.selectedSink2Depth;
                $scope.sinkRadius2 = $scope.diagramData.data.raw_data.sinks.css.sinkRadius2;
                $scope.sinkmeasure2top = $scope.diagramData.data.raw_data.sinks.css.sinkmeasure2top;
                $scope.sinkmeasure2width = $scope.diagramData.data.raw_data.sinks.css.sinkmeasure2width;
                //faucet data
                $scope.faucet1Option = $scope.diagramData.data.raw_data.faucets.faucet1;
                $scope.faucet2Option = $scope.diagramData.data.raw_data.faucets.faucet2;
                $scope.faucetCenter1 = $scope.diagramData.data.raw_data.faucets.faucet1distance;
                $scope.faucetCenter2 = $scope.diagramData.data.raw_data.faucets.faucet2distance;
                $scope.faucet11top = $scope.diagramData.data.raw_data.faucets.css.faucet11top;
                $scope.faucet12top = $scope.diagramData.data.raw_data.faucets.css.faucet12top;
                $scope.faucet13top = $scope.diagramData.data.raw_data.faucets.css.faucet13top;
                $scope.faucet21top = $scope.diagramData.data.raw_data.faucets.css.faucet21top;
                $scope.faucet22top = $scope.diagramData.data.raw_data.faucets.css.faucet22top;
                $scope.faucet23top = $scope.diagramData.data.raw_data.faucets.css.faucet23top;
                $scope.faucet11left = $scope.diagramData.data.raw_data.faucets.css.faucet11left;
                $scope.faucet12left = $scope.diagramData.data.raw_data.faucets.css.faucet12left;
                $scope.faucet13left = $scope.diagramData.data.raw_data.faucets.css.faucet13left;
                $scope.faucet21left = $scope.diagramData.data.raw_data.faucets.css.faucet21left;
                $scope.faucet22left = $scope.diagramData.data.raw_data.faucets.css.faucet22left;
                $scope.faucet23left = $scope.diagramData.data.raw_data.faucets.css.faucet23left;
                $scope.faucet12bottom = $scope.diagramData.data.raw_data.faucets.css.faucet12bottom;
                $scope.faucet22bottom = $scope.diagramData.data.raw_data.faucets.css.faucet22bottom;
                $scope.faucet12right = $scope.diagramData.data.raw_data.faucets.css.faucet12right;
                $scope.faucet22right = $scope.diagramData.data.raw_data.faucets.css.faucet22right;
                $scope.cssFaucetCenter1 = $scope.diagramData.data.raw_data.faucets.css.cssFaucetCenter1;
                $scope.cssFaucetCenter2 = $scope.diagramData.data.raw_data.faucets.css.cssFaucetCenter2;
                $scope.faucet1Rotate = $scope.diagramData.data.raw_data.faucets.css.faucet1Rotate;
                $scope.faucet2Rotate = $scope.diagramData.data.raw_data.faucets.css.faucet2Rotate;
            }, function errorCallback(response) {
                console.log('Loading products produced the following error: ' + response);
            }
        );
    };
}]);
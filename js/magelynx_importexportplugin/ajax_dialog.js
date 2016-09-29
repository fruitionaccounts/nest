ML_AjaxDialog = {
    per_portion:1,
    current_position: 0,
    mode: 'stop',
    session:'',
    url:'',
    controls_id:'ml_controls',
    timer:0,
    current_id: 'ml_current',
    dialog_id: 'ml_dialog',
    log_entry_class :'ml_log_entry',
    log_id : 'ml_log',
    timer_id : 'ml_timer',
    _timeout_id : null,   
    worksheets : [], 
    _pad : function(value){
        return (value.toString().length < 2) ? '0' + value : value;
    },
    
    dialog: function(msg){
        this.pause();
        $$('body')[0].addClassName('dialog-active')
        $(this.dialog_id).update(msg);

    },
    _timer: function(clear, reset, stop){            
        if(clear && this._timeout_id ){
            clearTimeout(this._timeout_id);
        }
        if(reset){
            this.timer = 0;
        }
        if(!stop){
            this._timeout_id = setTimeout(function(){
                if( ['stop', 'pause'].indexOf(this.mode) == -1 ){
                    this.timer+=250/1000;
                    this._timer();

                }
            }.bind(this), 250);
        } 

        var timer = this.timer;

        if(parseInt(this.timer) == this.timer){
            $(this.timer_id).update(this._pad(parseInt(timer / 3600)) + ' h. '
                                    +this._pad(parseInt(timer / 60)) + ' m. ' 
                                    + this._pad(timer % 60) + ' s.');
        }

    },

    log: function(msg){
        if($(this.current_id).innerHTML.replace(/^\s+|\s+$/g, "").length>0){
            $(this.log_id).insert({'top': '<div class="'+this.log_entry_class+'">'+ $(this.current_id).innerHTML +'</div>' });
        }
        var timer  = this.timer;
        var timestamp = this._pad(parseInt(timer / 3600)) + ':'+this._pad(parseInt(timer / 60)) + ':' +this._pad(parseInt(timer) % 60);
        $(this.current_id).update(timestamp + " " + msg);
    },   
  
    start: function(){
        //alert(JSON.stringify(this));
        this.current_position =  0;
        if(this.mode != 'stop'){
            throw Error('Can start only while in "stop" mode');
        }
        this.mode = 'process';      

        $(this.log_id).update();
        if(typeof this.startCallback != 'undefined'){
            this.startCallback();
        }
 
        this.log('Start');
        this._timer(true, true); //clear timeout, reset timer
        this.sendAjax({}, function(transport){
                $('loading-mask').hide();
                var obj = transport.responseText.evalJSON();
                if(obj.out){
                    this.log(obj.out);
                }
                if(obj.dialog){
                    this.dialog(obj.dialog);
                }
                if(obj.reset_ids){
                    this.ids = obj.reset_ids;
                    this.current_position = 0;
                }

                this.session= obj.session; 
        }.bind(this));

    },

    process: function(){
        if(this.mode != 'process'){
            throw Error('Can process only while in "process" mode');
        }

        this.log('Processing next load...');
        var ids_to_send = this.ids.slice(this.current_position, Math.min(this.current_position+this.per_portion,this.ids.length));

        if(typeof this.processCallback != 'undefined'){
            this.processCallback();
        }
        
        var flag_diff = false;
        var curr_k = false;
        var clean = [];
        ids_to_send.each(function(id){            
//            alert(id);
//alert(typeof  curr_k);
            if(id.split('-').length == 2){
                if(curr_k !== false &&  id.split('-')[0] != curr_k){
                    flag_diff = true;
//                    alert(flag_diff);
                }
                if(curr_k === false){
                    curr_k = id.split('-')[0];
                }
            }
            if(!flag_diff){
                clean.push(id);
            }

        });
        if(flag_diff){
            ids_to_send = clean;
        }

        if(ids_to_send.length){
            this.sendAjax({'product': ids_to_send.join(','), 'session' : this.session, 'ws' : this.worksheets.join(',') }, function(transport){
                    $('loading-mask').hide();
                    var obj = transport.responseText.evalJSON();
                    var obj = transport.responseText.evalJSON();
                    if(obj.out){
                        this.log(obj.out);
                    }
                    if(obj.dialog){
                        this.dialog(obj.dialog);
                    }

                    if(obj.reset_ids){
                        this.ids = obj.reset_ids;
                        this.current_position = 0;
                        if(this.mode=='process'){
                            //this.current_position += this.per_portion;
                            this.process();
                        }

                    }else{
                        if(this.mode=='process'){
                            this.current_position += ids_to_send.length;
                            this.process();
                        }
                    }
                    
            }.bind(this));

        }else{
            this.current_position = Math.min(this.current_position+this.per_portion,this.ids.length);
            this.finish();
        }



    },

    finish: function(){
        if(this.mode != 'process'){
            throw Error('Can finish only while in "process" mode');
        }

        this.log('Finish');
        this._timer(true, true, true);//clear timeout, reset, stop ticker
        this.mode = 'stop';

        this.log("---");//a "hack"
        $(this.current_id).update();
//        $(this.timer_id).update();
        this.session='';
        $(this.dialog_id).update();
        if(typeof this.finishCallback != 'undefined'){
            this.finishCallback();
        }

    },

    pause: function(){
        if(this.mode != 'process'){
            throw Error('Can process only in while "process" mode');
        }
        this.mode = 'pause';

        this._timer(true, false, true);//clear timeout, don't reset, stop ticker
        this.log('Pause');
    },

    resume: function(){
        if(this.mode != 'pause'){
            throw Error('Can resume only while in "pause" mode');
        }

        $$('body')[0].removeClassName('dialog-active')
        this.mode = 'process';
        this.log('Resume');
        this._timer();
        
        this.process();

    },

    stop: function(){
        if(this.mode != 'process' && this.mode != 'pause'){
            throw Error('Can stop only while in "process" or "pause" mode');
        }
        this.mode = 'stop';

        this.log('Stop');
        this._timer(true, true, true);//clear timeout, reset, stop ticker

        this.log("---");//a "hack"
        $(this.current_id).update();
//        $(this.timer_id).update();
        this.session='';
        $(this.dialog_id).update();

        
        $$('body')[0].removeClassName('dialog-active')
        if(typeof this.stopCallback != 'undefined'){
            this.stopCallback();
        }

    },
    

    sendAjax: function(_parameters, _onComplete){
        if(!this.url.length){
            throw Error('Ajax URL not set');
        }
        _parameters.session = this.session;
        new Ajax.Request(this.url, {
                method: 'POST',
                parameters: _parameters,
                onComplete: _onComplete,
                onFailure: function() { 

                    $('loading-mask').hide();
                    alert('Something went wrong. Try reloading page'); }
            });

    }
}

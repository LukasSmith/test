var dynamicHandler = function(){
    var handler = this;
    
    this.handle = function(type, response){
        switch(type){
            case 'transmission':
                return new transmissionHandler().handle(response);
            case 'show':
                return new showHandler().handle(response);
        }
    };
    
    this.request = function(type, url, params){
        $.post(url, params, function(data){
            if(data.hasOwnProperty('alerts') && data.alerts != null){
                $.each(data.alerts, function(){
                    if(this.isVisible == true)
                        new alertHandler().handle(this);
                });
            }
            
            if(data.response.status == true){
                if(data.hasOwnProperty('response'))
                    handler.handle(type, data.response);
            }
        }, 'json');
    };
    
};

var transmissionHandler = function(){
    var handler = this;

    this.handle = function(response){
        if(response.hasOwnProperty('handling')){
            switch(response.handling){
                case 'addmessage':
                    return handler.addMessageHandler(response);
                case 'gettransmission':
                    return handler.getTransmissionHandler(response);
                case 'setprivaterejected':
                    return handler.setPrivateRejectedHandler(response);
                case 'setgrouprejected':
                    return handler.setGroupRejectedHandler(response);
                case 'setprivate':
                    return handler.setPrivateHandler(response);
                case 'setgroup':
                    return  handler.setGroupHandler(response);
                case 'setpublic':
                    return handler.setPublicHandler(response);
                case 'setstatus':
                    return handler.setStatusHandler(response);
                case 'enablegroup':
                    return handler.enableGroupHandler(response);
                case 'enableprivate':
                    return handler.enablePrivateHandler(response);
                case 'disablegroup':
                    return handler.disableGroupHandler(response);
                case 'disableprivate':
                    return handler.disablePrivateHandler(response);
            }
        }
    };
    
    this.addMessageRequest = function(){
        var requestURL = '/ajax/transmission/addmessage';
        var messageContainer = $('.publisher-chat-container .message input');
        if(messageContainer.val().length > 0){
            new dynamicHandler().request('transmission', requestURL, {
                message : messageContainer.val()
            });
        }
    }
    
    this.getTransmissionRequest = function(){
        var requestURL = '/ajax/transmission/gettransmission';
        new dynamicHandler().request('transmission', requestURL, {});
    }
    
    this.setPrivateRejectedRequest = function(nick){
        var requestURL = '/ajax/transmission/setprivaterejected';
        new dynamicHandler().request('transmission', requestURL, {nick: nick});
    }
    
    this.setGroupRejectedRequest = function(nick){
        var requestURL = '/ajax/transmission/setgrouprejected';
        new dynamicHandler().request('transmission', requestURL, {nick: nick});
    }
    
    this.setPrivateRequest = function(nick){
        var requestURL = '/ajax/transmission/setprivate';
        new dynamicHandler().request('transmission', requestURL, {nick: nick});
    }
    
    this.setGroupRequest = function(nick){
        var requestURL = '/ajax/transmission/setgroup';
        new dynamicHandler().request('transmission', requestURL, {nick: nick});
    }
    
    this.setPublicRequest = function(){
        var requestURL = '/ajax/transmission/setpublic';
        new dynamicHandler().request('transmission', requestURL, {});
    }
    
    this.setStatusRequest = function(){
        var requestURL = '/ajax/transmission/setstatus';
        new dynamicHandler().request('transmission', requestURL, {
            status: $('.status-container .status input').val()
        });
    }
    
    this.enableGroupRequest = function(){
        var requestURL = '/ajax/transmission/enablegroup';
        var requestParams = {
            credits: $('.group-settings-container .credits input').val(),
            duration: $('.group-settings-container .duration input').val(),
            description: $('.group-settings-container .description input').val()
        };
        new dynamicHandler().request('transmission', requestURL, requestParams);
    }
    
    this.enablePrivateRequest = function(){
        var requestURL = '/ajax/transmission/enableprivate';
        var requestParams = {
            credits: $('.private-settings-container .credits input').val(),
            duration: $('.private-settings-container .duration input').val(),
            description: $('.private-settings-container .description input').val()
        };
        new dynamicHandler().request('transmission', requestURL, requestParams);
    }
    
    this.disableGroupRequest = function(){
        var requestURL = '/ajax/transmission/disablegroup';
        new dynamicHandler().request('transmission', requestURL, {});
    }
    
    this.disablePrivateRequest = function(){
        var requestURL = '/ajax/transmission/disableprivate';
        new dynamicHandler().request('transmission', requestURL, {});
    }
    
    this.addMessageHandler = function(response){
        $('.publisher-chat-container .message input').val('');
    }
    
    this.getTransmissionHandler = function(response){
        var messagesContainer = $('.publisher-chat-container .messages');
        var groupRequestsContainer = $('.group-requests-container .requests');
        var privateRequestsContainer = $('.private-requests-container .requests');
        
        messagesContainer.html('');
        groupRequestsContainer.html('');
        privateRequestsContainer.html('');
        
        $('.viewers').html(response.viewers);
        
        if(response.messages != null){
            $.each(response.messages, function(){
                messagesContainer.append('<div class="' + this.type.toLowerCase() + '"><b>' + this.nick + ':</b> ' + this.message + '</div>');
            });
            messagesContainer.prop({scrollTop: messagesContainer.prop("scrollHeight")});
        }
        
        if(response.grouprequests != null){
            $.each(response.grouprequests, function(){
                groupRequestsContainer.append('<div>' + this.nick + ' (10 credits)<span><a href="javascript:void(0);" onclick="objTransmissionHandler.setGroupRejectedRequest(\'' + this.nick + '\');">reject</a></span></div>');
            });
        }
        
        if(response.privaterequests != null){
            $.each(response.privaterequests, function(){
                privateRequestsContainer.append('<div>' + this.nick + '<span><a href="javascript:void(0);" onclick="objTransmissionHandler.setPrivateRejectedRequest(\'' + this.nick + '\');">reject</a> <a href="javascript:void(0);" onclick="objTransmissionHandler.setPrivateRequest(\'' + this.nick + '\');">start</a></span></div>');
            });
        }
    }
    
    this.setPrivateRejectedHandler = function(response){
        alert('HANDLE setPrivateRejectedHandler');
    }
    
    this.setGroupRejectedHandler = function(response){
        alert('HANDLE setGroupRejectedHandler');
    }
    
    this.setPrivateHandler = function(response){
        alert('HANDLE setPrivateHandler');
    }
    
    this.setGroupHandler = function(response){
        alert('HANDLE setGroupHandler');
    }
    
    this.setPublicHandler = function(response){
        alert('HANDLE setPublicHandler');
    }
    
    this.setStatusHandler = function(response){
        alert('HANDLE setStatusHandler');
    }
    
    this.enableGroupHandler = function(response){
        alert('HANDLE enableGroupHandler');
    }
    
    this.enablePrivateHandler = function(response){
        alert('HANDLE enablePrivateHandler');
    }
    
    this.disableGroupHandler = function(response){
        alert('HANDLE disableGroupHandler');
    }
    
    this.disablePrivateHandler = function(response){
        alert('HANDLE disablePrivateHandler');
    }
    
};

var showHandler = function(nick){   
    var nick = nick;
    var handler = this;

    this.handle = function(response){
        if(response.hasOwnProperty('handling')){
            switch(response.handling){
                case 'getshow':
                    return handler.getShowHandler(response);
                case 'addmessage':
                    return handler.addMessageHandler(response);
                case 'addprivate':
                    return handler.addPrivateHandler(response);
                case 'addgroup':
                    return handler.addGroupHandler(response);
                case 'addtip':
                    return handler.addTipHandler(response);
            }
        }
    };

    this.getShowRequest = function(){
        var requestURL = '/ajax/show/getshow';
        new dynamicHandler().request('show', requestURL, {
            nick: nick
        });
    }
    
    this.addMessageRequest = function(){
        var requestURL = '/ajax/show/addmessage';
        var messageContainer = $('.player-chat-container .message input');
        if(messageContainer.val().length > 0){
            new dynamicHandler().request('show', requestURL, {
                message : messageContainer.val(),
                nick : nick
            });
        }
    }
    
    this.addPrivateRequest = function(){
        var requestURL = '/ajax/show/addprivate';
        new dynamicHandler().request('show', requestURL, {
            nick: nick
        });
    }
    
    this.addGroupRequest = function(){
        var requestURL = '/ajax/show/addgroup';
        new dynamicHandler().request('show', requestURL, {
            nick: nick
        });
    }
    
    this.addTipRequest = function(){
        var requestURL = '/ajax/show/addtip';
        new dynamicHandler().request('show', requestURL, {
            nick: nick
        });
    }
    
    this.getShowHandler = function(response){
        var messagesContainer = $('.player-chat-container .messages');
        
        messagesContainer.html('');
        if(response.messages != null){
            $.each(response.messages, function(){
                messagesContainer.append('<div class="' + this.type.toLowerCase() + '"><b>' + this.nick + ':</b> ' + this.message + '</div>');
            });
            messagesContainer.prop({scrollTop: messagesContainer.prop("scrollHeight")});
        }
        
        if(response.show.status != null && response.show.status.length > 0){
            $('.show-status').css('display', 'block');
            $('.show-status .show-status-container').html(response.show.status);
        }else{
            $('.show-status').css('display', 'none');
        }
        
        if(response.show.is_group_enabled == true){
            $('.show-group').css('display', 'block');
            $('.show-requests-container .group').removeClass('inactive');
            $('.show-group-requests-header .credits').html(response.grouprequestscredits + ' / ' + response.show.group_credits);
            $('.show-group-description-container').html(response.show.group_description);
            $('.show-requests-container .group .credits').html(10);
            $('.show-requests-container .group .duration').html(response.show.group_duration);
            
            $('.show-group-requests-container .requests').html('');
            if(response.grouprequests != null){
                $.each(response.grouprequests, function(){
                    $('.show-group-requests-container .requests').append('<div><b>' + this.nick + '</b> <span>' + this.credits + ' credits</span></div>');
                });
            }   
        }else{
            $('.show-group').css('display', 'none');
            $('.show-requests-container .group').addClass('inactive');
        }
        
        if(response.show.is_private_enabled == true){
            $('.show-private').css('display', 'block');
            $('.show-requests-container .private').removeClass('inactive');
            $('.show-requests-container .private .credits').html(response.show.private_credits);
            $('.show-requests-container .private .duration').html(response.show.private_duration);
            $('.show-private-description-container').html(response.show.private_description);
        }else{
            $('.show-private').css('display', 'none');
            $('.show-requests-container .private').addClass('inactive');
        }
    }
    
    this.addMessageHandler = function(response){
        $('.player-chat-container .message input').val('');
    }
    
    this.addPrivateHandler = function(response){
        alert('HANDLE addPrivateHandler');
    }
    
    this.addGroupHandler = function(response){
        alert('HANDLE addGroupHandler');
    }
    
    this.addTipHandler = function(response){
        alert('HANDLE addTipHandler');
    }
    
};

var validator = function(){
    this.isnotempty = function(data){
        if(data.toString().length > 0)
            return true;
        return false;
    };
};

var alertHandler = function(){
    this.handle = function(data){
        alert(data.message);
    };
};
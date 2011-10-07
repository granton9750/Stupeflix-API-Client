var PgBar = {
  
  initialize: function(){
    this.view = new Element('div').addClass('PgBar');
    this.inner = this.view.appendChild(new Element('div').addClass('inner'));
  },
  
  _buildProxyUrl: function(action){
    return "./proxy.php?action=" + action;
  },
    
  start: function(successCallback, errorCallback){
    
    this.inner.style.width = "0%";
    this._successCallback = successCallback;
    this._errorCallback = errorCallback;

    new Request.JSON({ method: "get", url: this._buildProxyUrl('generate'),
      onComplete: function(response){
        if(response.error){
          this._errorCallback(response.error);
        }else{
          this.getStatus();
        }
      }.bind(this)
    }).send();
  },
    
  getStatus: function(){
    clearTimeout(this._statusTimer);
    new Request.JSON({ method: "get", url: this._buildProxyUrl('getStatus'),
      onComplete: this._processStatus.bind(this)
    }).send();
  },
  
  _processStatus: function(data){
    var status = data[0].status // get the status of the first profile
    
    if(status.error){
      this._errorCallback(status.error);
    }else{
      
      // update the view
      var percent = {
        'queued': 10,
        'starting': 20,
        'downloading': 25,
        'info': 29,
        'generating': 30 + Math.round(status.complete * 0.6),
        'uploading': 99
      }[status.status];
      this.inner.style.width = percent + "%";
      
      if(status['available-0-url']){ // the first upload is complete (stupeflix default upload in our case)
        this._getVideoUrl();
      }else{
        // poll for the generation status every 2 seconds
        this._statusTimer = setTimeout(this.getStatus.bind(this), 2000);
      }
    }
  },
  
  _getVideoUrl: function(){
    new Request.JSON({ method: "get", url: this._buildProxyUrl('getVideoUrl'),
      onComplete: this._successCallback
    }).send();
  }
  
}
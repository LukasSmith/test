package custom.main{
	
	import flash.net.*;
	import flash.media.*;
	import flash.events.*;
	import flash.display.*;
	import flash.external.*;
	
	public class Publisher extends Sprite{
		
		//variables
		private var streamidVar:String;
		private var streamurlVar:String;
		private var streamqualityVar:String;
		
		//objects
		private var objVideo:Video;		
		private var objCamera:Camera;
		private var objStream:NetStream;
		private var objMicrophone:Microphone;
		private var objConnection:NetConnection;
		private var objH264Video:H264VideoStreamSettings;
		
		public function Publisher(){		
			NetConnection.prototype.onBWDone = basicErrorHandler;
			NetConnection.prototype.onFCSubscribe = basicErrorHandler;
        }
		
		public function setStreamURL(streamurlPar:String = ''){
			streamurlVar = streamurlPar;
		}
		
		public function setStreamID(streamidPar:String = ''){
			streamidVar = streamidPar;
		}
		
		public function setObjects(){
			objCamera = Camera.getCamera();
			
			if(objCamera){
				objMicrophone = Microphone.getMicrophone();
				//objMicrophone.rate = 11;
				objMicrophone.codec = SoundCodec.SPEEX;
				objMicrophone.noiseSuppressionLevel = -30;
				objMicrophone.setUseEchoSuppression(true);
				objMicrophone.setSilenceLevel(0, 5000);
				objMicrophone.encodeQuality = 5;
				
				//if(streamqualityVar == 'HD'){
					//objCamera.setMode(1280, 720, 30, true);
				//}else{
					
				//}
				
				objCamera.setMode(540, 405, 25, true);
				objCamera.setQuality(0, 100);
				//objCamera.setKeyFrameInterval(10);
				
				objVideo = new Video(320, 240);
				objVideo.x = 0;
				objVideo.y = 0;
				objVideo.attachCamera(objCamera);
				addChild(objVideo);
			}
			
			return objCamera;
		}
		
		public function startBroadcast(){
			if(objConnection == null || !objConnection.connected){
				objConnection = new NetConnection();		
				objConnection.addEventListener(NetStatusEvent.NET_STATUS, startBroadcastHandler);
				objConnection.addEventListener(SecurityErrorEvent.SECURITY_ERROR, basicErrorHandler);
				objConnection.connect(streamurlVar);
			}
		}
		
		private function startBroadcastHandler(event:NetStatusEvent):void {
			if(event.info.code == "NetConnection.Connect.Success"){
				objH264Video = new H264VideoStreamSettings();
				objH264Video.setProfileLevel(H264Profile.BASELINE, H264Level.LEVEL_3);
				objH264Video.setMode(540, 405, 25);
				objH264Video.setQuality(0, 100);
				//objH264Video.setKeyFrameInterval(10);
				
				objStream = new NetStream(objConnection);
				objStream.videoStreamSettings = objH264Video;
				objStream.attachCamera(objCamera);
				objStream.attachAudio(objMicrophone);
				objStream.publish(streamidVar);
			}
		}
		
		public function startTransmission(){
			var objRequest:URLRequest = new URLRequest("/ajax/transmission/addtransmission");
			objRequest.method = URLRequestMethod.POST; 
			
			//var objSettings:Object = ExternalInterface.call("getTransmissionSettings");
    
			//var paramsVar:URLVariables = new URLVariables(); 
			//paramsVar.groupEnabled = objSettings["groupEnabled"];
			//paramsVar.groupCredits = objSettings["groupCredits"];
			//paramsVar.groupDuration = objSettings["groupDuration"];
			//paramsVar.groupDescription = objSettings["groupDescription"];
			//paramsVar.privateEnabled = objSettings["privateEnabled"];
			//paramsVar.privateCredits = objSettings["privateCredits"];
			//paramsVar.privateDuration = objSettings["privateDuration"];
			//paramsVar.privateDescription = objSettings["privateDescription"];
    
			//objRequest.data = paramsVar;  
            
			var objLoader:URLLoader = new URLLoader(objRequest); 
    		objLoader.addEventListener(Event.COMPLETE, startTransmissionHandler); 
			objLoader.dataFormat = URLLoaderDataFormat.TEXT; 
			objLoader.load(objRequest);
		}
		
		private function startTransmissionHandler(event:Event):void{
			var objResponse:Object = JSON.parse(event.target.data);
			if(objResponse["response"]["code"].length == 32){
				this.setStreamID(objResponse["response"]["code"]);
				startBroadcast();
			}
		}
		
		public function stopTransmission(){
			var objRequest:URLRequest = new URLRequest("/ajax/transmission/endtransmission");
			objRequest.method = URLRequestMethod.POST;  
                
			var objLoader:URLLoader = new URLLoader(objRequest); 
    		objLoader.addEventListener(Event.COMPLETE, stopTransmissionHandler); 
			objLoader.dataFormat = URLLoaderDataFormat.TEXT; 
			objLoader.load(objRequest);
		}
		
		private function stopTransmissionHandler(event:Event):void{
			var objResponse:Object = JSON.parse(event.target.data);
			if(objResponse["response"]["status"] == true){
				if(objStream != null)
					objStream.close();
				if(objConnection != null)
					objConnection.close();
			}
		}
		
		private function basicErrorHandler():void{}

	}
}
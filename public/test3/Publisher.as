package{
	
	import flash.display.Sprite;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetStream;	
	import flash.net.NetConnection;
	import flash.media.Video;
	import flash.media.Camera;
	import flash.media.Microphone;
	
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
		
		public function setStreamQuality(streamqualityPar:String = ''){
			streamqualityVar = streamqualityPar;
		}
		
		public function setObjects(){
			objCamera = Camera.getCamera();
			
			if(objCamera){
				objMicrophone = Microphone.getMicrophone();

				if(streamqualityVar == 'HD'){
					objCamera.setMode(1280, 720, 30, true);
				}else{
					objCamera.setMode(320, 240, 15, true);
				}
				
				objCamera.setQuality(0, 0);
				//objCamera.setKeyFrameInterval(24);
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
				objConnection.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
				objConnection.addEventListener(SecurityErrorEvent.SECURITY_ERROR, basicErrorHandler);
				objConnection.connect(streamurlVar);
			}
		}
		
		public function stopBroadcast(){
			if(objStream != null){
				objStream.close();
				objConnection.close();
			}
		}
		
		private function netStatusHandler(event:NetStatusEvent):void {
			if(event.info.code == "NetConnection.Connect.Success"){
				objStream = new NetStream(objConnection);
				objStream.attachAudio(objMicrophone);
				objStream.attachCamera(objCamera);
				objStream.publish(streamidVar);
			}
		}
		
		private function basicErrorHandler():void{}

	}
}
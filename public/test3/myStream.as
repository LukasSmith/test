package{
	
	import flash.display.Sprite;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetStream;	
	import flash.net.NetConnection;
	import flash.media.Video;
	import flash.media.Camera;
	import flash.media.Microphone;
	
	public class myStream extends Sprite{
		
		private var serverurl:String;
		private var streamid:String;
		private var stream:NetStream;		
		private var connection:NetConnection;
		private var video:Video;		
		private var camera:Camera;
		private var microphone:Microphone;		
		
		public function myStream(initialId:String = '', initialUrl:String = ''){
			streamid = initialId;
			serverurl = initialUrl;			
			NetConnection.prototype.onBWDone = basicErrorHandler;
			NetConnection.prototype.onFCSubscribe = basicErrorHandler;
        }
		
		public function startBroadcast(){
			if(connection == null || !connection.connected){
				connection = new NetConnection();		
				connection.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
				connection.addEventListener(SecurityErrorEvent.SECURITY_ERROR, basicErrorHandler);
				connection.connect(serverurl);
			}else{
				broadcast();
			}
		}
		
		public function stopBroadcast(){
			if(stream != null){
				stream.close();
			}
		}
		
		public function preapre(){
			camera = Camera.getCamera();
			if(camera){
				microphone = Microphone.getMicrophone();
				camera.setMode(480, 360, 24, false);
				camera.setQuality(0, 90);
				camera.setKeyFrameInterval(24);
				video = new Video(320, 240);
				video.x = 0;
				video.y = 0;
				video.attachCamera(camera);
				addChild(video);
			}
			return camera;
		}
		
		public function broadcast(){
			stream = new NetStream(connection);
			stream.attachCamera(camera);
			stream.attachAudio(microphone);
			stream.publish(streamid);
			trace('aaaaaaaa');
		}
		
		private function netStatusHandler(event:NetStatusEvent):void {
			switch(event.info.code) {
				case "NetConnection.Connect.Success":
					broadcast();
					break;
			}
		}
		
		private function basicErrorHandler():void{}

	}
}
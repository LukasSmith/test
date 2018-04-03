package {
	
	import flash.display.*;
	import flash.events.*;
	import flash.text.*;
	
	import flash.net.FileReference;
	import flash.net.FileReferenceList;
	import flash.net.FileFilter;
	import flash.net.URLRequest;
	import flash.utils.Timer;
	import flash.events.TimerEvent;
	
	
	public class Uploader extends MovieClip {
		
		var file:FileReference;
		var filefilters:Array;
		var req:URLRequest;
		var tm:Timer;
		var speed:Number = 0;
		var currbytes:Number = 0;
		var lastbytes:Number = 0;
		
		public function Uploader(){
			req = new URLRequest();
			req.url = ( stage.loaderInfo.parameters.f )? stage.loaderInfo.parameters.f : 'http://dev.cbesslabs.com/flashtuts/flash_uploader/upload.php';
			file = new FileReference();
			setup( file );
			select_btn.addEventListener( MouseEvent.CLICK, browse );
			progress_mc.bar.scaleX = 0;
			tm = new Timer( 1000 );
			tm.addEventListener( TimerEvent.TIMER, updateSpeed );
			cancel_btn.addEventListener( MouseEvent.CLICK, cancelUpload );
			cancel_btn.visible = false;
		}
		
		public function browse( e:MouseEvent ){
			filefilters = [ new FileFilter('Images', '*.jpg') ]; // add other file filters
			file.browse( filefilters );
		}
		
		private function setup( file:FileReference ){
			file.addEventListener( Event.CANCEL, cancel_func );
			file.addEventListener( Event.COMPLETE, complete_func );
			file.addEventListener( IOErrorEvent.IO_ERROR, io_error );
			file.addEventListener( Event.OPEN, open_func );
			file.addEventListener( ProgressEvent.PROGRESS, progress_func );
			file.addEventListener( Event.SELECT, selectHandler );
			file.addEventListener( DataEvent.UPLOAD_COMPLETE_DATA, show_message );		
		}
		
		private function cancel_func( e:Event ){
			trace( 'canceled !' );
		}
		
		private function complete_func( e:Event ){
			trace( 'complete !' );
		}
		
		private function io_error( e:IOErrorEvent ){
			var tf = new TextFormat();
			tf.color = 0xff0000;
			label_txt.defaultTextFormat = tf;
			label_txt.text = 'The file could not be uploaded.';
			tm.stop();
			cancel_btn.visible = false;
			select_btn.visible = true;
		}
		
		private function open_func( e:Event ){
			//trace( 'opened !' );
			tm.start();
			cancel_btn.visible = true;
			select_btn.visible = false;
		}
		
		private function progress_func( e:ProgressEvent ){
			progress_mc.bar.scaleX = e.bytesLoaded / e.bytesTotal;
			var tf = new TextFormat();
			tf.color = 0x000000;
			label_txt.defaultTextFormat = tf;
			label_txt.text = Math.round( (e.bytesLoaded/e.bytesTotal)*100)+'% uploaded '+speed+' kb/s';
			currbytes = e.bytesLoaded;
		}
		
		private function selectHandler( e:Event ){
			file.upload( req );
			
		}
		
		private function show_message( e:DataEvent ){
			tm.stop();
			var tf = new TextFormat();
			if( e.data == 'ok' ){
				tf.color = 0x009900;
				label_txt.defaultTextFormat = tf;
				label_txt.text = 'The file has been uploaded.';
			} else if( e.data == 'error'){
				tf.color = 0xff0000;
				label_txt.defaultTextFormat = tf;
				label_txt.text = 'The file could not be uploaded.';
			}
		}
		
		private function updateSpeed( e:TimerEvent ){
			speed = Math.round( (currbytes - lastbytes)/1024 );
			lastbytes = currbytes;
		}
		
		private function cancelUpload( e:MouseEvent ){
			file.cancel();
			reset();
		}
		
		private function reset(){
			cancel_btn.visible = false;
			select_btn.visible = true;
			label_txt.text = '';
			progress_mc.bar.scaleX = 0;
		}
		
	}	
}
package {
	
	import com.adobe.images.*;
	
	import flash.ui.*;
	import flash.net.*;
	import flash.geom.*;
	import flash.text.*;
	import flash.utils.*;
	import flash.events.*;
	import flash.display.*;

    public class imageUploader{
		
		var varFile:FileReference;
		var varFileFilters:Array;
		var txtMessages:TextField;

		public function imageUploader(parMessages:TextField){
			this.txtMessages = parMessages;
		}
		
		public function browseHandler(e:MouseEvent){
			this.varFileFilters = [new FileFilter('Images', '*.jpg')];
			this.varFile = new FileReference();
			this.varFile.addEventListener(Event.SELECT, this.selectHandler);
			this.varFile.addEventListener(IOErrorEvent.IO_ERROR, this.ioErrorHandler);
			this.varFile.browse(varFileFilters);
		}
		
		private function selectHandler(e:Event){
			this.txtMessages.text = "Loading file...";
			this.varFile.addEventListener(Event.COMPLETE, this.fileLoadCompleteHandler);
            this.varFile.addEventListener(IOErrorEvent.IO_ERROR, this.ioErrorHandler);
            this.varFile.load();
		}
		
		private function fileLoadCompleteHandler(event:Event):void{
			var varLoader:Loader = new Loader();
            varLoader.contentLoaderInfo.addEventListener(Event.COMPLETE, this.dataLoadCompleteHandler);
            varLoader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, this.ioErrorHandler);
            varLoader.loadBytes(this.varFile.data);
		}
		
		private function dataLoadCompleteHandler(event:Event):void{
			this.txtMessages.text = "Sending file...";
			var basicBitmapData:BitmapData = Bitmap(event.target.content).bitmapData;
			
			var jpgEncoder:JPGEncoder = new JPGEncoder(100);
			var varImageFile:ByteArray = jpgEncoder.encode(basicBitmapData);

			var varReq:URLRequest = new URLRequest();
			varReq.url = "http://ns3002445.ovh.net/ajax/account/addimage";
			varReq.requestHeaders = new Array(new URLRequestHeader("Content-Type", "image/jpeg"));
			varReq.contentType = "image/jpeg";
			varReq.method = URLRequestMethod.POST;
			varReq.data = varImageFile;
				 
			var varLoader:URLLoader = new URLLoader();
			varLoader.addEventListener(Event.COMPLETE, this.dataUploadCompleteHandler);
			varLoader.dataFormat = URLLoaderDataFormat.BINARY;          
			varLoader.load(varReq);
		}
		
		private function dataUploadCompleteHandler(event:Event):void{
			this.txtMessages.text = "Ready";
		}
		
		private function ioErrorHandler(event:IOErrorEvent):void{
			
		}
	
    }
}
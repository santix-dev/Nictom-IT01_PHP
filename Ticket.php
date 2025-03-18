<?php
// ticket.php

date_default_timezone_set("America/Argentina/San_juan");
// from https://phpqrcode.sourceforge.net/
require_once "../phpqrcode/qrlib.php";

class Ticket{
    private string $printerName;
    private int $paperWidth;
    private float $total;
    private string $text;
    private string $date;
    private int $ticketType;
    // should i add a method for setting default like a reset for the printer?
    public function __construct($printerName, $paperWidth=58)
    {
        $this->printerName=$printerName;
        $this->paperWidth=$paperWidth;
        $this->text="";
        $this->total=0;
        $this->date = date("d/m/y - H:i");
    }
    public function setTicketType(int $type=0)
    {
        $this->ticketType=$type;
        if ($this->ticketType==0)
        {
            $this->text.=str_pad("Producto", 17) . str_pad("Cant", 5) . str_pad("Precio", 10);
        }
        // example of type 0:
        // product      quant  price
        // fernet 750ml   1    $12000
        
        
        elseif ($this->ticketType==1)
        {
            // $this->setCenterAlign();
            $this->text.="Producto\n";
            // $this->setLeftAlign();
            $this->text.=str_pad("Cant", 5) ." x ". str_pad("Precio", 10) ." = ". str_pad("Subtotal", 10);
        }
        // example of type 1
        // product
        // quant  price  subtotal
        // Fernet 750ml
        // 2  x  $12000    $24000  
        $this->addSeparatorLine();
    }
    public function addSeparatorLine(){
        // adds a total width line of '-' 
        $this->normalCharSize();
        $this->crlf();
        $this->text.=str_repeat("-",32);
    }
    public function setHeader($text){
        $this->duplicateCharSize();
        $this->text.=$text;
        $this->crlf();
        $this->duplicateCharWidth();
        $this->text.=$this->date;
        $this->normalCharSize();
        $this->addSeparatorLine();
        
    }
    public function AddProduct(string $productName, float $quantity, float $price){
        $productSubTotal=$price*$quantity;
        $this->total+=$productSubTotal;
        if ($this->paperWidth==58)
        {
            if ($this->ticketType==0)
            {
                $this->text.= str_pad(substr($productName,0,16), 17) . str_pad(strval($quantity), 5) . str_pad("$".strval($price), 10) . "\n";
            }
            elseif ($this->ticketType==1)
            {
                $this->text.= str_pad($productName, 31) . "\n";
                $this->text.= str_pad(strval($quantity), 5) ."x ". str_pad(strval($price), 15) ;
                // $this->setRightAlign();
                $this->text.="$". strval($productSubTotal) . "\n";
            }

        }

        // $this->setShrinkInterlined();
        // $this->addSeparatorLine();
        // $this->setNormalInterlined();

        // $this->crlf();

    }
    // the next 3 methods aren't working fine so i don't recomend them. if you use any of these and the printer 
    // starts to print different and setNormalShrinked doesn't works, you should 
    // run setDefaultSettings method to "reset" it in some way
    // i have a comment about this at the beggining of the class
    public function setShrinkInterlined(){
        $this->text.="\x1B\x33\x10";
    }
    public function setNormalInterlined(){
        $this->text.="\x1B\x33\x20";
    }
    public function setBigInterlined(){
        $this->text.="\x1B\x33\x30";
    }
    public function testProducts(){
        $this->setDefaultConfig();
        $this->setTicketType(0);
        $this->AddProduct("Fernet Branca 750ml",2,12000);
        $this->AddProduct("Coca 2.25",2,3800);
        $this->addTotal();
        $this->printTicket();
    }
    public function testHeader()
    {
        
        $this->setCenterAlign();
        $this->strongStart();
        $this->duplicateCharSize();
        $this->text.="Texto de prueba\n";
        $this->normalCharSize();
        $this->strongFinish();
        $this->duplicateCharWidth();
        $this->text.=$this->date;
        $this->normalCharSize();
        $this->setRightAlign();
        $this->crlf();
        $this->text.=str_repeat("-",32);
        $this->printTicket();

    }
    public function addTotal(){
        $this->addSeparatorLine();
        $this->text.=str_pad("Total:",22) . str_pad("$".strval($this->total),10);
    }
    public function addRounding(float $rounding){
        // $this->addSeparatorLine();
        $this->total-=$rounding;
        $this->text.=str_pad("Redondeo:",20) . str_pad("- $".strval($rounding),10);
    }
    public function crlf(){
        $this->text.="\n";
    }
    public function setLeftAlign(){
        $this->text.="\x1B\x61\x00";
    }
    public function setRightAlign(){
        $this->text.="\x1B\x61\x02";
    }
    public function setCenterAlign(){
        $this->text.="\x1B\x61\x01";
    }
    public function underlinedStart(){
        $this->text.="\x1B\x2D\x01";
    }
    public function underlinedOff(){
        $this->text.="\x1B\x2D\x00";
    }
    public function setDefaultConfig(){
        // set default settings:
        // - left align
        // - normal size char
        // - not strong words
        $this->text.="\x1B\x40";
    }
    public function strongStart(){
        // set start of strong words
        $this->text.="\x1B\x45\x01";
    }
    public function strongFinish(){
        $this->text.="\x1B\x45\x00";
    }
    public function duplicateCharWidth(){
        // only duplicates once
        // sets off any other duplication setting 
        $this->text.="\x1B\x21\x20";
    }
    public function duplicateCharHeight(){
        // only duplicates once
        // sets off any other duplication setting 
        $this->text.="\x1B\x21\x10";
    }
    public function duplicateCharSize(){
        // only duplicates once
        // sets off any other duplication setting 
        $this->text.="\x1B\x21\x30";
    }
    public function normalCharSize(){
        // sets off any duplication setting 
        $this->text.="\x1B\x21\x00";
    }
    public function littleCharSize(){
        // sets off any duplication setting 
        $this->text.="\x1B\x4D\x01";
    }
    
    public function printTicket(){
        $archivo = "ticket.txt";
        $this->text.="\n\n\n";
        file_put_contents($archivo, $this->text);
        copy($archivo, "//localhost/$this->printerName");
        unlink($archivo);
    }
    public function printBarcode($data, $type = 4) {
        /*
        Tipos de códigos de barras compatibles:
        - 0: UPC-A
        - 1: UPC-E
        - 2: JAN13 (EAN-13)
        - 3: JAN8 (EAN-8)
        - 4: CODE39
        - 5: ITF
        - 6: CODABAR
        - 7: CODE93
        - 8: CODE128
        */
        
        $this->text .= "\x1D\x77\x02"; // Establece el ancho del código de barras
        $this->text .= "\x1D\x68\x50"; // Establece la altura (80 píxeles)
        $this->text .= "\x1D\x66\x01"; // Texto debajo del código de barras
        
        // Imprime el código de barras
        $this->text .= "\x1D\x6B" . chr($type) . $data . "\x00";
        
        $this->crlf(); // Salto de línea
    }
    // now it works!!!
    public function addImage($imagePath, $maxWidth = 384) {
        // 1. Cargar la imagen según su tipo.
        $info = getimagesize($imagePath);
        if (!$info) {
            throw new Exception("No se pudo obtener la información de la imagen.");
        }
        $mime = $info['mime'];
        
        switch ($mime) {
            case 'image/png':
                $img = imagecreatefrompng($imagePath);
                break;
            case 'image/jpeg':
                $img = imagecreatefromjpeg($imagePath);
                break;
            default:
                throw new Exception("Formato de imagen no soportado. Solo PNG y JPG están implementados.");
        }
        
        // 2. Redimensionar la imagen si es necesario.
        $width  = imagesx($img);
        $height = imagesy($img);
        
        // Si la imagen supera el ancho máximo, se calcula el factor de escala
        if ($width > $maxWidth) {
            $ratio     = $maxWidth / $width;
            $newWidth  = $maxWidth;
            $newHeight = intval($height * $ratio);
        } else {
            $newWidth  = $width;
            $newHeight = $height;
        }
        
        // Es requisito que el ancho en píxeles sea múltiplo de 8. 
        if ($newWidth % 8 !== 0) {
            $newWidth = $newWidth - ($newWidth % 8);
            // Ajustamos proporcionalmente la altura.
            $newHeight = intval($newHeight * ($newWidth / $width));
        }
        
        $resizedImg = imagecreatetruecolor($newWidth, $newHeight);
        // Rellenar con fondo blanco.
        $white = imagecolorallocate($resizedImg, 255, 255, 255);
        imagefill($resizedImg, 0, 0, $white);
        imagecopyresampled($resizedImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        // 3. Convertir la imagen a blanco/negro.
        // Recorrer cada píxel y aplicar umbral simple (por ejemplo, 128).
        for ($y = 0; $y < $newHeight; $y++) {
            for ($x = 0; $x < $newWidth; $x++) {
                $rgb = imagecolorat($resizedImg, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8)  & 0xFF;
                $b = $rgb & 0xFF;
                // Umbral simple en la media.
                $gray = ($r + $g + $b) / 3;
                if ($gray < 128) {
                    $color = imagecolorallocate($resizedImg, 0, 0, 0);
                } else {
                    $color = imagecolorallocate($resizedImg, 255, 255, 255);
                }
                imagesetpixel($resizedImg, $x, $y, $color);
            }
        }
        
        // 4. Generar los comandos ESC/POS para imprimir la imagen en modo ráster:
        // El comando "GS v 0" tiene la siguiente estructura:
        // [1D 76 30 m xL xH yL yH d...]
        // m: modo (0 = densidad normal, 8-dot vertical)
        //
        // La imagen se envía como filas consecutivas donde cada byte representa 8 píxeles horizontales.
        $widthBytes = $newWidth / 8; // ancho en bytes
        $xL = $widthBytes & 0xFF;
        $xH = ($widthBytes >> 8) & 0xFF;
        $yL = $newHeight & 0xFF;
        $yH = ($newHeight >> 8) & 0xFF;
        
        // Comando de cabecera.
        $cmd  = chr(0x1D) . chr(0x76) . chr(0x30) . chr(0x00);
        $cmd .= chr($xL) . chr($xH) . chr($yL) . chr($yH);
        
        // Convertir la imagen en datos binarios.
        $data = "";
        for ($y = 0; $y < $newHeight; $y++) {
            for ($xByte = 0; $xByte < $widthBytes; $xByte++) {
                $byte = 0;
                // Cada byte representa 8 píxeles horizontales
                for ($bit = 0; $bit < 8; $bit++) {
                    $x = $xByte * 8 + $bit;
                    // Obtenemos el color del píxel: asumimos 0 (negro) o 0xFFFFFF (blanco)
                    $col = imagecolorat($resizedImg, $x, $y);
                    // Comparamos: si es negro, encendemos el bit.
                    if ($col == 0x000000) {
                        $byte |= (1 << (7 - $bit));
                    }
                }
                $data .= chr($byte);
            }
        }
        
        // Concatenamos el comando y el contenido de la imagen al texto del ticket.
        $this->text .= $cmd . $data;
        
        // Liberar memoria.
        imagedestroy($img);
        imagedestroy($resizedImg);
    }
    

    public function addText($text) {
        $this->text.=$text;
    }
    public function addQR($text) {
        $qrFile = $this->generateQRImage($text, "qrcode.png");
        $this->addImage($qrFile);
    }
    public function generateQRImage($text, $filename = "qrcode.png", $errorCorrectionLevel = "L", $matrixPointSize = 8, $margin = 2) {
        // La función QRcode::png permite especificar el nombre de archivo donde se guarda la imagen.
        QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);
        return $filename;
    }
    

}


if (get_included_files()[0]==__FILE__)
{
    $printer = new Ticket("Nictom IT01"); // Supposing that you named your printer like i did
    $printer->setDefaultConfig();
    $printer->setCenterAlign();
    // $printer->littleCharSize();

    $printer->addImage("logo.png");
    $printer->addQR("https://instagram.com/sannntix_17");
    // Imprimir Código de Barras
    // $printer->printBarcode("123456789012", 4);

    // Enviar a la impresora
    $printer->printTicket(); // Supongamos que hay un método print() que envía los datos a la impresora


}

?>
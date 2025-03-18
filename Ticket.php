<?php
date_default_timezone_set("America/Argentina/San_juan");
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
        $this->text.="Ticket de prueba sunset\n";
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
    
    public function printTicket(){
        $archivo = "ticket.txt";
        $this->text.="\n\n\n";
        file_put_contents($archivo, $this->text);
        copy($archivo, "//localhost/$this->printerName");
        unlink($archivo);
    }


}


if (get_included_files()[0]==__FILE__)
{
    $ticket = new Ticket("Nictom IT01");
    // $ticket->testHeader();
    // $ticket->testProducts();
    // my liqquor store's name
    $ticket->setHeader("Sunset Drugstore");
    $ticket->setTicketType(1);
    $ticket->AddProduct("Fernet buhero",3,9350.50);
    $ticket->AddProduct("Fernet branca 710",1,12000);
    $ticket->AddProduct("CocaCola 2.25L Desc",4,3800);
    $ticket->addTotal();
    $ticket->printTicket();

}

?>
<?php 

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Client;
use App\Models\Product;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Storage;

class DevisController extends Controller
{
    public function index()
    {
        $devis = Devis::with('client')->get();
      
        
        return view('devis.index', compact('devis'));
    }

    public function create()
    {
        $clients = Client::all();
       
        $products = Product::all();
        return view('devis.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $devis_name = $this->generateDevisName();
        $amount = $this->calculateAmount($request->items);

        $devis = Devis::create([
            'devis_name' => $devis_name,
            'client_id' => $request->client_id,
            'amount' => $amount,
        ]);
   
        foreach ($request->items as $item) {
            $devis->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'observations' => $item['observations'],
            ]);
        }

        return redirect()->route('devis.index');
    }

    public function edit($id)
    {
        $devis = Devis::with('items')->findOrFail($id);
        $clients = Client::all();
        $products = Product::all();
        $items = Item::all(); // Fetch or define your items here
        return view('devis.edit', compact('devis', 'clients', 'products','items'));
    }

    public function update(Request $request, $id)
    {
        $devis = Devis::findOrFail($id);
        $devis->update($request->only('devis_name', 'client_id', 'amount'));

        $devis->items()->delete();
      
        foreach ($request->items as $item) {
           
            $devis->items()->create([
                'product_name' => $item['product_name'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'observations' => $item['observations'],
            ]);
        }
      
        return redirect()->route('devis.index');
    }

    public function destroy($id)
    {
        $devis = Devis::findOrFail($id);
        $devis->delete();

        return redirect()->route('devis.index');
    }



    public function generatePdf($id)
    {
        $devis = Devis::with('client', 'items.product')->findOrFail($id);
        $invoiceNumber = $devis->devis_name; // Use devis_name as the invoice number
    
        $html = view('devis.pdf', compact('devis'))->render();
    
        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML($html);
        $pdfContent = $html2pdf->output('', 'S'); // 'S' returns the PDF as a string

        $clientFolder = 'public/clients/' . $devis->client->client_id.'/';
    
        // Check if the folder exists, if not, create it
        if (!Storage::exists($clientFolder)) {
            Storage::makeDirectory($clientFolder);
        }
        $devisFolder = 'public/clients/' . $devis->client->client_id.'/devis';
    
        // Check if the folder exists, if not, create it
        if (!Storage::exists($devisFolder)) {
            Storage::makeDirectory($devisFolder);
        }
        $pdfPath = $devisFolder . '/' . $invoiceNumber . '.pdf';
    
        Storage::put($pdfPath, $pdfContent);
    
        return response()->json(['pdfPath' => Storage::url('app/'.$pdfPath)]);
    }
    

    private function generateDevisName()
    {
        $currentYearMonth = Carbon::now()->format('Ym');
        $lastDevis = Devis::where('devis_name', 'LIKE', 'DE-' . $currentYearMonth . '%')->orderBy('devis_id', 'desc')->first();

        if ($lastDevis) {
            $lastNumber = (int)substr($lastDevis->devis_name, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return 'DE-' . $currentYearMonth . $newNumber;
    }


    private function calculateAmount($items)
    {
        $amount = 0;
        foreach ($items as $item) {
            $itemTotal = $item['quantity'] * $item['price'] - $item['discount'];
            $amount += $itemTotal;
        }
        return $amount;
    }

}

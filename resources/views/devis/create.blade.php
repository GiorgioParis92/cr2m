@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Devis</h1>
    <form action="{{ route('devis.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="client_id">Client</label>
            <select required class="form-control" id="client_id" name="client_id">
                @foreach($clients as $client)
                <option value="{{ $client->client_id }}">{{ $client->client_title }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="items">Items</label>
            <table class="table" id="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="calcul">
                        <td>
                            <select required name="items[0][product_id]" class="form-control item-field">
                                <option value="">Choisir un produit</option>

                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" class="form-control item-field"></td>
                        <td><input type="number" step="0.01" name="items[0][price]" class="form-control item-field"></td>
                        <td><input type="number" step="0.01" name="items[0][discount]" class="form-control item-field"></td>
                        <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
                    </tr>
                    <tr class="observations">
                        <td colspan="4">
                            <textarea class="form-control" name="items[0][observations]"></textarea>
                        </td>
   
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success" id="add-item">Add Item</button>
        </div>
        <table class="table">
            <tr>
                <th>Total H.T.</th>
                <td class="text-end"  id="total-amount">0.00</td>
            </tr>
            <tr>
                <th>Total TVA</th>
                <td class="text-end" id="total-tva">0.00</td>
            </tr>
            <tr>
                <th>Total TVA</th>
                <td class="text-end" id="total-ttc">0.00</td>
            </tr>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;

        function updateTotalAmount() {
            let totalAmount = 0;
            document.querySelectorAll('#items-table tbody tr.calcul').forEach(row => {
                const quantity = parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('[name*="[price]"]').value) || 0;
                const discount = parseFloat(row.querySelector('[name*="[discount]"]').value) || 0;
                totalAmount += (quantity * price) - (quantity * price*discount/100);
            });
            document.getElementById('total-amount').innerText = totalAmount.toFixed(2);
            document.getElementById('total-tva').innerText = (totalAmount*0.2).toFixed(2);
            document.getElementById('total-ttc').innerText = (totalAmount*1.2).toFixed(2);
        }

        document.getElementById('add-item').addEventListener('click', function () {
            const tableBody = document.querySelector('#items-table tbody');
            const newRow = document.createElement('tr');
            const newRow2 = document.createElement('tr');
            newRow.classList.add('calcul');
        newRow2.classList.add('observations');
            newRow.innerHTML = `
                <td>
                    <select required name="items[${itemIndex}][product_id]" class="form-control item-field">
                        <option value="">Choisir un produit</option>

                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-field"></td>
                <td><input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control item-field"></td>
                <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" class="form-control item-field"></td>
                <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
            `;


            newRow2.innerHTML = `<td colspan="4"><textarea class="form-control" name="items[${itemIndex}][observations]"></textarea></td>`;

tableBody.appendChild(newRow);
tableBody.appendChild(newRow2);
            itemIndex++;
            updateTotalAmount();
        });

        document.querySelector('#items-table').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            const calculRow = e.target.closest('tr.calcul');
            const observationsRow = calculRow.nextElementSibling;
            if (observationsRow && observationsRow.classList.contains('observations')) {
                observationsRow.remove();
            }
            calculRow.remove();
            updateTotalAmount();
        }
    });

        document.querySelector('#items-table').addEventListener('input', function (e) {
            if (e.target.classList.contains('item-field')) {
                updateTotalAmount();
            }
        });

        updateTotalAmount(); // Initial calculation
    });
</script>
@endsection

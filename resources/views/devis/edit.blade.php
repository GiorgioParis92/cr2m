@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Edit Devis</h1>

        <form action="{{ route('devis.update', $devis->devis_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="client_id">Client</label>
                <select required class="form-control" id="client_id" name="client_id">
                    <option value="">Choisir un produit</option>

                    @foreach ($clients as $client)
                        <option value="{{ $client->client_id }}" @if ($client->client_id == $devis->client_id) selected @endif>
                            {{ $client->client_title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="items">Items</label>
                <table class="table" id="items-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Remise</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devis->items as $index => $item)
                            <tr class="calcul">
                                <td>
                                    <select style="display: @if ($item->product_id == '-1') ? none : block @endif"
                                        name="items[{{ $index }}][product_id]" class="form-control item-field"
                                        id="productSelect{{ $index }}"
                                        onchange="handleProductChange({{ $index }})">
                                        <option value="">Choisir un produit</option>
                                        <option @if ($item->product_id == '-1') ? selected : '' @endif value="-1">
                                            Champ libre</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                @if ($product->id == $item->product_id) selected @endif>{{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input style="display: @if ($item->product_id == '-1') ? block : none @endif"
                                        type="text" name="items[{{ $index }}][product_name]"
                                        value="{{ $item->product_name }}" class="form-control"
                                        id="productNameInput{{ $index }}">
                                </td>


                                <td><input type="number" name="items[{{ $index }}][quantity]"
                                        class="form-control item-field" value="{{ $item->quantity }}"></td>
                                <td><input type="number" step="0.01" name="items[{{ $index }}][price]"
                                        class="form-control item-field" value="{{ $item->price }}"></td>
                                <td><input type="number" step="0.01" name="items[{{ $index }}][discount]"
                                        class="form-control item-field" value="{{ $item->discount }}"></td>
                                <td rowspan="2"><button type="button" class="btn btn-danger remove-item">Remove</button>
                                </td>
                            </tr>
                            <tr class="observations">
                                <td colspan="4">
                                    <textarea class="form-control" name="items[{{ $index }}][observations]">{{ $item->observations }}</textarea>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" id="add-item">Add Item</button>
            </div>
            <table class="table">
                <tr>
                    <th>Total H.T.</th>
                    <td class="text-end" id="total-amount">0.00</td>
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
            <div class="btn btn-secondary" id="generate-pdf-btn" data-id="{{ $devis->devis_id }}">Generate PDF</div>

        </form>
    </div>

    <div class="loader">
        <img src="{{ asset('storage/images/logo.png') }}" alt="Loading...">
    </div>
    <style>
        .loader {
            background: #00000075;
            width: 100%;
            top: 0;
            left: 0;
            margin: auto;
            height: 100vh;
            display: none;
            position: absolute;
        }

        .loader img {
            width: 20%;
            height: auto;
            animation: spin 3s linear infinite;
            position: absolute;
            top: 40%;
            left: 35%;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        iframe {
            min-height: 80vh;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            margin-top: 2%;
            min-height: 90vh;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#generate-pdf-btn').on('click', function() {
                $('.loader').show()
                var id = $(this).data('id');

                $.ajax({
                    url: `../${id}/pdf`,
                    method: 'GET',
                    success: function(data) {
                        if (data.pdfPath) {

                            $('#pdfFrame').attr('src', '../../..' + data.pdfPath);
                            $('#pdfModal').css('display', 'block');
                            $('.loader').hide()
                        }
                    }
                });
            });

            // Close the modal when the user clicks on <span> (x)
            $('.close').on('click', function() {
                $('#pdfModal').css('display', 'none');
            });

            // Close the modal when the user clicks anywhere outside of the modal
            $(window).on('click', function(event) {
                if (event.target == $('#pdfModal')[0]) {
                    $('#pdfModal').css('display', 'none');
                }
            });
            $(document).on('keydown', function(event) {
    console.log("Key pressed: ", event.key); // Debugging line to see which key is pressed
    if (event.key === 'Escape' || event.key === 'Esc' || event.keyCode === 27) {
        var modal = $('#pdfModal');
        if (modal.is(':visible')) {
            modal.css('display', 'none');
            console.log("Modal closed"); // Debugging line to confirm the modal is being closed
        }
    }
});
        });

        function handleProductChange(index) {
            var select = document.getElementById('productSelect' + index);
            var input = document.getElementById('productNameInput' + index);

            if (select && input) {
                if (select.value == '-1') {
                    select.style.display = 'none';
                    input.style.display = 'block';
                    // input.value = '';
                } else {
                    select.style.display = 'block';
                    input.style.display = 'none';

                    var selectedOption = select.options[select.selectedIndex];
                    input.value = selectedOption.text;
                }
            }
        }

        // Initialize the input field with the selected product name if a product is already selected
        document.addEventListener('DOMContentLoaded', function() {
            @if (isset($items))
                @foreach ($items as $index => $item)
                    @if ($item->product_id != -1)
                        handleProductChange({{ $index }});
                    @endif
                @endforeach
            @endif
        });
        document.addEventListener('DOMContentLoaded', function() {






            let itemIndex = {{ $devis->items->count() }};

            function updateTotalAmount() {
                let totalAmount = 0;
                document.querySelectorAll('#items-table tbody tr.calcul').forEach(row => {
                    const quantity = parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
                    const price = parseFloat(row.querySelector('[name*="[price]"]').value) || 0;
                    const discount = parseFloat(row.querySelector('[name*="[discount]"]').value) || 0;
                    totalAmount += (quantity * price) - (quantity * price * discount / 100);
                });
                document.getElementById('total-amount').innerText = totalAmount.toFixed(2);
                document.getElementById('total-tva').innerText = (totalAmount * 0.2).toFixed(2);
                document.getElementById('total-ttc').innerText = (totalAmount * 1.2).toFixed(2);
            }

            document.getElementById('add-item').addEventListener('click', function() {
                const tableBody = document.querySelector('#items-table tbody');
                const newRow = document.createElement('tr');
                const newRow2 = document.createElement('tr');

                newRow.classList.add('calcul');
                newRow2.classList.add('observations');

                newRow.innerHTML = `
            <td>
                <select required name="items[${itemIndex}][product_id]" class="form-control item-field">
                    <option value="">Choisir un produit</option>
                    @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-field"></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control item-field"></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" class="form-control item-field"></td>
            <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
        `;

                newRow2.innerHTML =
                    `<td colspan="4"><textarea class="form-control" name="items[${itemIndex}][observations]"></textarea></td>`;

                tableBody.appendChild(newRow);
                tableBody.appendChild(newRow2);

                itemIndex++;
                updateTotalAmount();
            });

            document.querySelector('#items-table').addEventListener('click', function(e) {
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

            document.querySelector('#items-table').addEventListener('input', function(e) {
                if (e.target.classList.contains('item-field')) {
                    updateTotalAmount();
                }
            });

            updateTotalAmount(); // Initial calculation
        });
    </script>
@endsection

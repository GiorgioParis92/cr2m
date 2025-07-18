<div>
    <div class="row form-group">
        <div class="mb-2 mb-sm-0 col-12 col-md-3">
            <label class="mr-sm-2">Client</label>
            <input type="text" class="form-control" wire:model.change="clientName"
                placeholder="Filtrer par nom de client">
        </div>

        <div class="mb-2 mb-sm-0 col-12 col-md-3">
            <label class="mr-sm-2">Précarité</label>
            <select class="no_select2 form-control" wire:model="precarite" id="precarite">
                <option value="">Filtrer par type de ménage</option>
                <option value="bleu">Bleu</option>
                <option value="jaune">Jaune</option>
                <option value="violet">Violet</option>
                <option value="rose">Rose</option>
            </select>
        </div>

        <div class="mb-2 mb-sm-0 col-12 col-md-3">
            <label class="mr-sm-2">Étape</label>
            <select  class="no_select2 form-control" wire:model="etape" id="etape">
                <option value="">Filtrer par étape</option>
                @foreach ($etapes as $etape)
                    <option value="{{ $etape->id }}">{{ $etape->etape_icon }} - {{ $etape->etape_desc }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2 mb-sm-0 col-12 col-md-3">
            <label class="mr-sm-2">Statut</label>
            <select class="no_select2 form-control" wire:model="statut" id="statut">
                <option value="">Filtrer par statut</option>
                @foreach ($status as $statut)
                    <option value="{{ $statut->status_desc }}">{{ $statut->status_desc }}</option>
                @endforeach
            </select>
        </div>
        @if (
            (auth()->user()->client_id > 0 && auth()->user()->client->type_client == 2) ||
                (auth()->user()->client_id > 0 && auth()->user()->client->type_client == 1) ||
                auth()->user()->client_id == 0)
            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Accompagnateur</label>
                <select class="no_select2 form-control" data-column="13" wire:model="accompagnateur"
                    id="accompagnateur">
                    <option value="">Filtrer par accompagnateur</option>

                    @foreach ($mars as $mar)
                        <option value="{{ $mar->id }}">{{ $mar->client_title }}</option>
                    @endforeach

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Mandataire</label>
                <select class="no_select2 form-control" data-column="15" wire:model="mandataire" id="mandataire">
                    <option value="">Filtrer par mandataire</option>

                    @foreach ($financiers as $financier)
                        <option value="{{ $financier->id }}">{{ $financier->client_title }}</option>
                    @endforeach

                </select>
            </div>


            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Apporteur</label>
                <select class="no_select2 form-control" data-column="17" wire:model="installateur" id="installateur">
                    <option value="">Filtrer par apporteur</option>

                    @foreach ($installateurs as $installateur)
                        <option value="{{ $installateur->id }}">{{ $installateur->client_title }}</option>
                    @endforeach

                </select>
            </div>

            @php
            $occupations=['proprietaire'=>"Propriétaire",'proprietaire_bailleur'=>"Propriétaire Bailleur",'sci'=>"SCI"];
            @endphp

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Type de propriétaire</label>
                <select class="no_select2 form-control" data-column="22" wire:model="occupation" id="occupation">
                    <option value="">Filtrer par type de propriétaire<option>

                        @foreach ($occupations as $key=>$value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach

                        {{-- <option value="proprietaire">Propriétaire<option>
                        <option value="proprietaire_bailleur">Propriétaire Bailleur<option>
                        <option value="sci">SCI<option> --}}

                </select>
            </div>

            <div class="mb-2 mb-sm-0 col-12 col-md-3">
                <label class="mr-sm-2">Département</label>
                <select class="no_select2 form-control" data-column="21" wire:model="dpt" id="dpt">
                    <option value="">Filtrer par département</option>

                    @foreach ($departments as $dpt)
                        <option value="{{ $dpt['departement_code'] }}">{{ $dpt['departement_code'] }} -
                            {{ $dpt['departement_nom'] }}</option>
                    @endforeach

                </select>
            </div>

        @endif
    </div>


    <div class="row">
        <h4><span id="count_total">0</span> Résultats</h4>
        <div class="col-lg-4">
        <select class="form-control no_select2 col-lg-4" id="pageSizeSelector">
            <option value="10">10 lignes</option>
            <option value="20">20 lignes</option>
            <option selected value="50">50 lignes</option>
            <option value="100">100 lignes</option>
            <option value="99999999">Tout Afficher</option>
          </select>
        </div>
    </div>
    <!-- AgGrid Container with wire:ignore -->
    <div id="myGrid" class="ag-theme-alpine" style="height: 80vh; width: 100%;" wire:ignore></div>


</div>

<!-- Include AgGrid scripts -->
{{-- <script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.noStyle.js"></script>
<script src="https://unpkg.com/ag-grid-enterprise/dist/ag-grid-enterprise.min.js"></script> --}}
{{-- <script src="https://unpkg.com/ag-grid-community@29.0.0/dist/ag-grid-community.min.noStyle.js"></script> --}}
<script src="https://unpkg.com/ag-grid-community@29.0.0/dist/ag-grid-community.noStyle.js"></script>
<script src="https://unpkg.com/ag-grid-enterprise@29.0.0/dist/ag-grid-enterprise.min.js"></script>


<style>
span.badge.badge-danger {
    background: var(--bs-danger);
    margin-right:3px;
}
span.badge.badge-outline-danger {
    background: var(--bs-danger);
    margin-right:3px;
}
span.badge.badge-warning {
    background: var(--bs-warning);
    margin-right:3px;
}
span.badge.badge-success {
    background: var(--bs-success);
    margin-right:3px;
}
span.badge.badge-outline-danger {
    background: white;
    border: var(--bs-danger);
    border: 1px solid var(--bs-danger);
    color: var(--bs-danger);
    margin-right:3px;
}
.ag-watermark{display:none!important}
</style>
<script>
    // Declare variables in the global scope
    var gridApi;
    var isGridInitialized = false;
    var gridOptions;

    // Function to initialize the grid
    function initializeGrid(gridOptions) {
        const gridDiv = document.querySelector('#myGrid');
        if (!isGridInitialized) {
            new agGrid.Grid(gridDiv, gridOptions);
            isGridInitialized = true;
            console.log('Grid initialized');
        }
    }

    document.addEventListener('livewire:load', function() {

        // Update Livewire property when Select2 changes
        $('#precarite').on('change', function(e) {
            let value = $(this).val();
            @this.set('precarite', value);
        });
        $('#etape').on('change', function(e) {
            let value = $(this).val();
            @this.set('etape', value);
        });
        $('#accompagnateur').on('change', function(e) {
            let value = $(this).val();
            @this.set('accompagnateur', value);
        });
        $('#mandataire').on('change', function(e) {
            let value = $(this).val();
            @this.set('mandataire', value);
        });
        $('#installateur').on('change', function(e) {
            let value = $(this).val();
            @this.set('installateur', value);
        });
        $('#statut').on('change', function(e) {
            let value = $(this).val();
            @this.set('statut', value);
        });
        $('#dpt').on('change', function(e) {
            let value = $(this).val();
            @this.set('dpt', value);
        });
        $('#occupation').on('change', function(e) {
            let value = $(this).val();
            @this.set('occupation', value);
        });

        const pageSizeSelector = document.getElementById('pageSizeSelector');
        // pageSizeSelector.value = gridOptions.paginationPageSize; // e.g., 20
    pageSizeSelector.addEventListener('change', function(e) {
        var newPageSize = Number(e.target.value);
       
        if (gridApi && !isNaN(newPageSize)) {
            gridApi.paginationSetPageSize(newPageSize);
        }
    });

        const columnDefs = [
            {
                field: "fiche_name",
                headerName: "Type de dossier",
                sortable: true,
                enableRowGroup: true,
                sort: 'desc', // or 'desc' for descending order
                cellRenderer: fiche,

      
         
            },
            {
                field: "date_update",
                headerName: "Date de mise à jour",
                sortable: true,
                filter: 'agDateColumnFilter',
                enableRowGroup: true,
                sort: 'desc', // or 'desc' for descending order

                valueFormatter: (params) => {
                    return params.value ? new Date(params.value).toLocaleDateString('fr-FR') : '';
                },
                filterParams: {
                    // Custom comparator to handle date filtering
                    comparator: (filterDate, cellValue) => {
                        if (!cellValue) return -1; // No date in cell

                        // Parse the cell value into a Date object
                        const cellDate = new Date(cellValue);

                        // Compare the cell date with the filter date
                        if (cellDate < filterDate) return -1;
                        if (cellDate > filterDate) return 1;
                        return 0;
                    },
                    browserDatePicker: true, // Use browser's date picker for a localized experience
                },
            },
        {
                field: "date_creation",
                headerName: "Date de création",
                sortable: true,
                filter: 'agDateColumnFilter',
                enableRowGroup: true,
                sort: 'desc', // or 'desc' for descending order

                valueFormatter: (params) => {
                    return params.value ? new Date(params.value).toLocaleDateString('fr-FR') : '';
                },
                filterParams: {
                    // Custom comparator to handle date filtering
                    comparator: (filterDate, cellValue) => {
                        if (!cellValue) return -1; // No date in cell

                        // Parse the cell value into a Date object
                        const cellDate = new Date(cellValue);

                        // Compare the cell date with the filter date
                        if (cellDate < filterDate) return -1;
                        if (cellDate > filterDate) return 1;
                        return 0;
                    },
                    browserDatePicker: true, // Use browser's date picker for a localized experience
                },
            },
            {
                field: "reference_unique",
                headerName: "N° de dossier",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,

            },
            {
                field: "beneficiaire.nom",
                headerName: "Bénéficiaire",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                valueGetter: function(params) {
                    return params.data.beneficiaire.nom + ' ' + params.data.beneficiaire.prenom;
                },
                cellRenderer: render_cell_beneficiaire,
            },
            {
                field: "beneficiaire.adresse",
                headerName: "Adresse",
                sortable: true,
                filter: 'agSetColumnFilter',
                autoHeight: true,

                enableRowGroup: true,
                valueGetter: function(params) {
                    return params.data.beneficiaire.numero_voie + ' ' + params.data.beneficiaire
                        .adresse;
                },
                cellRenderer: render_cell_adresse,
            },
            {
                field: "beneficiaire.cp",
                headerName: "Code postal",
                hide: true,
            },
            {
                field: "beneficiaire.ville",
                headerName: "Ville",
                hide: true,
            },
            {
                field: "beneficiaire.telephone",
                headerName: "Téléphone",
                hide: true,
            },
            {
                field: "beneficiaire.email",
                headerName: "Email",
                hide: true,
            },
            {
                field: "etape",
                headerName: "Étape",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                cellRenderer: render_cell_etape,
            },
            {
                field: "statut",
                headerName: "Statut",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                cellRenderer: render_cell_status,

            },
            @if(auth()->user()->client_id==0)
        {
            field: "statut_anah",
            headerName: "Statut Anah",
            sortable: true,
            filter: 'agSetColumnFilter',
            enableRowGroup: true,
            autoHeight: true,
            cellRenderer: statut_anah,

          
        },
        @endif
            {
                field: "accompagnateur",
                headerName: "Accompagnateur",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,

                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
                cellRenderer: render_cell_accompagnateur,
            },
            {
                field: "mandataire",
                headerName: "Mandataire Financier",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
                cellRenderer: render_cell_mandataire,
            },
            {
                field: "installateur",
                headerName: "Apporteur",
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
                cellRenderer: render_cell_installateur,
            },


            {
                field: "last_rdv",
                headerName: "RDV",
                sortable: true,
                filter: 'agDateColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
                cellRenderer: render_rdv,
                filterParams: {
                    // Custom comparator to handle date filtering
                    comparator: (filterDate, cellValue) => {
                        if (!cellValue) return -1; // No date in cell

                        // Parse the cell value into a Date object
                        const cellDate = new Date(cellValue);

                        // Compare the cell date with the filter date
                        if (cellDate < filterDate) return -1;
                        if (cellDate > filterDate) return 1;
                        return 0;
                    },
                    browserDatePicker: true, // Use browser's date picker for a localized experience
                },
            },
            {
                field: "docs", 
                headerName: "Documents", 
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
                cellRenderer: render_cell_docs, 
            },
            {
                field: "beneficiaire.occupation", 
                headerName: "occupation", 
                sortable: true,
                filter: 'agSetColumnFilter',
                enableRowGroup: true,
                autoHeight: true,
                cellStyle: {
                    textAlign: 'center'
                },
            },

        ];
            console.log(@json($dossiers))
        // Define gridOptions in the global scope
        gridOptions = {
            rowData: [], // Start with empty data

            enableAdvancedFilter: false,
            columnDefs: columnDefs,
            rowData: @json($dossiers),
            domLayout: 'autoHeight',
            defaultExcelExportParams: {
                allColumns: true,
                // You can also specify process callbacks if needed
            },
            defaultColDef: {
                flex: 1,

                resizable: true,
                sortable: true,
                filter: true,
            },
            floatingFilter: true,
            animateRows: true,
            enableRangeSelection: true,
            pagination: true,
            paginationPageSize: 50,
            overlayLoadingTemplate: '<div class="ag-overlay-loading-center" style="padding: 10px;"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><br/>Chargement des données...</div>',

            // Custom no rows overlay
            overlayNoRowsTemplate: '<div class="ag-overlay-no-rows-center" style="padding: 10px;">Appliquez au moins 1 filtre</div>',

            groupDisplayType: 'groupRows', // Display groups as rows
            autoGroupColumnDef: {
                headerName: "Group",
                field: "category",
                cellRenderer: 'agGroupCellRenderer',
                cellRendererParams: {
                    // Configure expand/collapse icons
                    suppressCount: false,
                },
            },
            rowGroupPanelShow: 'always', // Show the row group panel at the top

            onGridReady: (params) => {
                gridApi = params.api;
                isGridInitialized = true;
                console.log('Grid API initialized:', params);
                var totalRows = params.api.getDisplayedRowCount();
                const allColumnIds = params.columnApi.getColumns().map(col => col.getColId());
                params.columnApi.autoSizeColumns(allColumnIds);
                $('#count_total').html(totalRows);
            },
            onFirstDataRendered: function(params) {
                params.api.sizeColumnsToFit();
                var totalRows = params.api.getDisplayedRowCount();
                $('#count_total').html(totalRows);

            },
            onGridSizeChanged: function(params) {
                params.api.sizeColumnsToFit();
                const allColumnIds = params.columnApi.getColumns().map(col => col.getColId());
                params.columnApi.autoSizeColumns(allColumnIds);
            },
            onModelUpdated: function(params) {
                // Get the number of displayed rows after any model update (filtering, sorting, etc.)
                var totalRows = params.api.getDisplayedRowCount();
                $('#count_total').html(totalRows);
            },
            onRowClicked: onRowClickedHandler, // Add the row click event handler here

            sideBar: {
                toolPanels: [{
                    id: "columns",
                    labelDefault: "Columns",
                    labelKey: "columns",
                    iconKey: "columns",
                    toolPanel: "agColumnsToolPanel",
                    toolPanelParams: {
                        suppressRowGroups: true,
                        suppressValues: true,
                        suppressPivots: true,
                        suppressPivotMode: true,
                        suppressColumnFilter: false,
                        suppressColumnSelectAll: false,
                        suppressColumnExpandAll: true,
                    },
                }, ],
                defaultToolPanel: "",
                rowHeight: 170,
            },
            localeText: {
                groupBy: 'Grouper par cette colonne',
                pageSize: 'Nombre de lignes par page',
                page: 'Page',
                more: 'Plus',
                to: 'à',
                of: 'de',
                next: 'Suivant',
                last: 'Dernier',
                first: 'Premier',
                previous: 'Précédent',
                loadingOoo: 'Chargement...',
                rowDragText: 'Déposez les colonnes ici pour grouper les lignes',
                groupColumns: 'Déposez les colonnes ici pour grouper les lignes',
                rowGroupColumnsEmptyMessage: 'Déposez les colonnes ici pour grouper les lignes',
                // If needed, you can also change other locale texts

                // ... other locale texts ...
            },
        };

        // Initialize the grid for the first time
        initializeGrid(gridOptions);

        Livewire.on('dossierDataUpdated', function(newData) {
            if (isGridInitialized && gridApi) {
                console.log('Updating row data with new data from Livewire event');
                console.log('New data:', newData);

                // Remove existing data
                const rowData = [];
                gridApi.forEachNode(function(node) {
                    rowData.push(node.data);
                });
                gridApi.applyTransaction({
                    remove: rowData
                });

                // Add new data
                gridApi.applyTransaction({
                    add: newData
                });

                $('#count_total').html(newData.length)

            } else {
                console.warn('Grid API not initialized yet. Reinitializing grid.');
                initializeGrid(gridOptions);
            }
        });
    });
    function onGridSizeChanged(params) {
        const allColumnIds = params.columnApi.getColumns().map(col => col.getColId());
        params.columnApi.autoSizeColumns(allColumnIds);
    }
    //     document.addEventListener('livewire:update', function() {
    //     if (isGridInitialized && gridApi) {
    //         console.log('gridApi methods:', Object.keys(gridApi));
    //         console.log('Updating row data');
    //         var newData = @json($dossiers);
    //         console.log('New data:', newData);

    //         // Remove existing data
    //         const rowData = [];
    //         gridApi.forEachNode(function(node) {
    //             rowData.push(node.data);
    //         });
    //         gridApi.applyTransaction({ remove: rowData });

    //         // Add new data
    //         gridApi.applyTransaction({ add: newData });
    //     } else {
    //         console.warn('Grid API not initialized yet. Reinitializing grid.');
    //         initializeGrid(gridOptions);
    //     }
    // });
    Livewire.hook('message.processed', (message, component) => {
        if (isGridInitialized && gridApi) {
            console.log('Livewire message processed, updating grid data');
            const newData = component.get('dossiers'); // Get the updated dossiers
            console.log('New data from message:', newData);

            // Remove existing data
            const rowData = [];
            gridApi.forEachNode(function(node) {
                rowData.push(node.data);
            });
            gridApi.applyTransaction({
                remove: rowData
            });

            // Add new data
            gridApi.applyTransaction({
                add: newData
            });
        } else {
            console.warn('Grid API not initialized yet. Reinitializing grid.');
            initializeGrid(gridOptions);
        }


    });
    function onRowClickedHandler(event) {
    // Get the dossier URL from the clicked row's data
    const dossierUrl = event.data.dossier_url;

    // Check if the URL exists before attempting to open it
    if (dossierUrl) {
        // Open the URL in a new tab
        window.open(dossierUrl, '_blank');
    } else {
        console.warn('No dossier URL found for this row.');
    }
}
    // Render cell functions
    function render_cell_beneficiaire(params) {
        const data = params.data;
        if (!data) {
            return '';
        }

        const container = document.createElement('div');

        const nameLink = document.createElement('a');
        nameLink.href = data.dossier_url;
        const boldText = document.createElement('b');
        boldText.textContent = `${data.beneficiaire.nom} ${data.beneficiaire.prenom}`;
        nameLink.appendChild(boldText);
        container.appendChild(nameLink);

        container.appendChild(document.createElement('br'));

        const divLink = document.createElement('a');
        divLink.href = data.dossier_url;
        const btnDiv = document.createElement('div');
        btnDiv.className = `btn bg-primary bg-${data.couleur_menage}`;
        btnDiv.textContent = data.texte_menage;
        divLink.appendChild(btnDiv);
        container.appendChild(divLink);

        return container;
    }

    function render_cell_adresse(params) {
        const data = params.data;
        if (!data) {
            return '';
        }
        return `
        <div style="line-height:18px">
            <div>${data.beneficiaire.numero_voie} ${data.beneficiaire.adresse}</div>
            <div>${data.beneficiaire.cp} ${data.beneficiaire.ville}</div>
            Tél : ${data.beneficiaire.telephone}<br />
            <span class="font-italic">email : ${data.beneficiaire.email}</span>
            </div>
        `;
    }

    function render_cell_etape(params) {
        const data = params.data;
        if (!data) {
            return '';
        }
        return `
            <a style="max-width:80px" target="_blank" href="${data.dossier_url}">
                <span class="badge badge-primary badge_button">${data.etape}</span>
                <div style="margin-top: 13px; max-width: 80px; text-wrap: wrap; font-size: 9px; padding: 8px !important; background-size: 0; padding-top: 13px !important; width: 100%; max-width: 100%;" class="btn btn-${data.etape_style}">
                    ${data.etape_desc}
                </div>
            </a>
        `;
    }
    function fiche(params) {
        const data = params.data;
        if (!data) {
            return '';
        }
        return `
            <a style="max-width:80px" target="_blank" href="${data.dossier_url}">
             
                <div style="margin-top: 13px; max-width: 80px; text-wrap: wrap; font-size: 9px; padding: 8px !important; background-size: 0; padding-top: 13px !important; width: 100%; max-width: 100%;" class="btn btn-${data.fiche_color}">
                    ${data.fiche_name}
                </div>
            </a>
        `;
    }
    function render_cell_status(params) {
        const data = params.data;
        if (!data) {
            return '';
        }

        if(data.subvention!='' && data.subvention!=undefined) {
            var subvention = 'Subvention : '+data.subvention+' €'
        } else {
            var subvention ='';
        }

        return `
                                    <a target="_blank"  href="${data.dossier_url}">
                                <div 
                                    class="btn btn-${data.statut_style}">

                                     ${data.statut}
                                    
                                </div>
                             
                            </a>
    
        `;
    }



    function statut_anah(params) {
        const data = params.data;
        if (!data) {
            return '';
        }

        if(data.subvention!='' && data.subvention!=undefined) {
            var subvention = 'Subvention : '+data.subvention+' €'
        } else {
            var subvention ='';
        }

        return `
                                    <a target="_blank"  href="${data.dossier_url}">
                                <div 
                                    class="btn btn-primary">

                                     ${data.statut_anah.replace('Dossier : ', '')}
                                    
                                </div>
                               <div >

                                     ${subvention}
                                    
                                </div>
                            </a>
    
        `;
    }

    function render_cell_accompagnateur(params) {
        const data = params.data;
        if (!data || !data.accompagnateur) {
            return '';
        }
        const imageUrl = data.accompagnateur_img ? `storage/${data.accompagnateur_img}` : null;
        const imageHtml = imageUrl ? `<img class="logo_table" src="${imageUrl}" alt="Accompagnateur Logo" />` : '';
        return `<div>${imageHtml}${data.accompagnateur}</div>`;
    }

    function render_cell_mandataire(params) {
        const data = params.data;
        console.log(data)
        if (!data || !data.mandataire || data.mandataire_id==0 || data.mandataire==null || data.mandataire==undefined) {
            return '';
        }
        const imageUrl = data.mandataire_img ? `storage/${data.mandataire_img}` : null;
        const imageHtml = imageUrl ? `<img class="logo_table" src="${imageUrl}" alt="Mandataire Logo" />` : '';
        return `<div>${imageHtml}${data.mandataire}</div>`;
    }

    function render_cell_installateur(params) {
        const data = params.data;
        if (!data || !data.installateur) {
            return '';
        }
        const imageUrl = data.installateur_img ? `storage/${data.installateur_img}` : null;
        const imageHtml = imageUrl ? `<img class="logo_table" src="${imageUrl}" alt="Installateur Logo" />` : '';
        return `<div>${imageHtml}${data.installateur}</div>`;
    }

    function render_rdv(params) {
        const data = params.data;
        if (!data || !data.rdv) {
            return '';
        }
        console.log(data.last_rdv)
        var rdvInfo = '';
        data.rdv.forEach(rdv => {
            console.log(rdv)
            const date = new Date(rdv.date_rdv);

            const dateOptions = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit'
            };

            const formattedDate = date.toLocaleDateString('fr-FR', dateOptions);
            const formattedTime = date.toLocaleTimeString('fr-FR', timeOptions);
            const formattedDateTime = `${formattedDate} à ${formattedTime}`;

            rdvInfo += '<a target="_blank"  href="' + data.dossier_url +
                '"><div class="show_rdv btn btn-' + (rdv.status ? rdv.status
                    .rdv_style : '') + '">RDV MAR' + rdv.type_rdv + ' du ' +
                formattedDateTime + ' Statut : ' + (rdv.status ? rdv.status
                    .rdv_desc : '') + '</div></a><br/>';
        });




        return rdvInfo;
    }
    function render_cell_docs(params) {
    let data = params.value;

    // If data is a JSON string, parse it
    if (typeof data === 'string') {
        try {
            data = JSON.parse(data);
        } catch (error) {
            console.error("Invalid JSON format:", error);
            return '';
        }
    }

    // Check if the data has the expected properties
    if (!data || (!data.missingDocs && !data.waitingForSignatureDocs && !data.signedDocs && !data.noSignatureRequested)) {
        return ''; // Return empty if no valid data found
    }

    // Helper function to create badge with hover tooltip
    function createBadge(label, count, docs,color) {
        if (count > 0) {
            return `<span class="badge badge-${color}" title="${docs.join(', ')}">${count}</span>`;
        }
        return '';
    }

    // Construct the badges
    let content = '';

    // Missing Docs Badge
    content += createBadge('Missing Docs', data.missingDocs.count, data.missingDocs.docs,'outline-danger');

    // Waiting for Signature Docs Badge
    content += createBadge('Waiting for Signature', data.waitingForSignatureDocs.count, data.waitingForSignatureDocs.docs,'warning');

    // Signed Docs Badge
    content += createBadge('Signed Docs', data.signedDocs.count, data.signedDocs.docs,'success');

    // No Signature Requested Docs Badge
    content += createBadge('No Signature Requested', data.noSignatureRequested.count, data.noSignatureRequested.docs,'danger');

    // Return the constructed content
    return content;
}


</script>

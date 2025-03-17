
{{--         HÍVÓKÓD   Kell hozzá a clientID

<button class="btn btn-primary btn-sm print-invoice" data-client-id="${item.id}}">Nyugta nyomtatása</button> --}}
   





   <!-- Modal ablak -->
   <div id="receiptModal" class="modal fade" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Nyugta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptContent">
                <!-- Ide kerül a dinamikusan betöltött nyugta tartalom -->
                @include('reports.receipt')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">Nyomtatás</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.print-invoice', function (e) {
    e.stopPropagation();
    const clientId = $(this).data('client-id'); // Az client ID lekérése a gomb adatattribútumából

    $.ajax({
        type: "GET",
        url: `/get-receipt-data-by-client/${clientId}`,
        dataType: "json",
        success: function (response) {
            if (response.error) {
                alert(response.error);
                return;
            }

            // A modal tartalmának frissítése
            $('#receiptContent').html(response.html);

            // Modal megnyitása
            $('#receiptModal').modal('show');
        },
        error: function (error) {
            console.error('Hiba történt az adatok lekérésekor:', error);
        }
    });
});

function printReceipt() {
            const printContent = document.getElementById('receiptContent').innerHTML;
            const originalContent = document.body.innerHTML;

            // Az oldal tartalmának cseréje a nyugta tartalmára
            document.body.innerHTML = printContent;

            // Nyomtatás indítása
            window.print();

            // Visszaállítás az eredeti tartalomra
            document.body.innerHTML = originalContent;

            // Az oldal újratöltése (opcionális)
            window.location.reload();
}
</script>
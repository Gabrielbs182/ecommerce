
        <?php
          // Cria um objeto de preferência
          $preference = new MercadoPagoPreference();

          // Cria um item na preferência
          $item = new MercadoPagoItem();
          $item->id = 'item-ID-1234';
          $item->title = 'Meu produto';
          $item->currency_id = 'BRL';
          $item->picture_url = 'https://www.mercadopago.com/org-img/MP3/home/logomp3.gif';
          $item->description = 'Descrição do Item';
          $item->category_id = 'art';
          $item->quantity = 1;
          $item->unit_price = 75.76;
          $preference->items = array($item);

          // Informações do pagador
          $payer = new MercadoPagoPayer();
          $payer->name = 'João';
          $payer->surname = 'Silva';
          $payer->email = 'user@email.com';
          $payer->phone = array(
              'area_code' => '11',
              'number' => '4444-4444'
          );
          $payer->identification = array(
              'type' => 'CPF',
              'number' => '19119119100'
          );
          $payer->address = array(
              'street_name' => 'Street',
              'street_number' => 123,
              'zip_code' => '06233200'
          );
          $preference->payer = $payer;

          // URLs de retorno
          $back_urls = array(
              'success' => 'https://www.success.com',
              'failure' => 'http://www.failure.com',
              'pending' => 'http://www.pending.com'
          );
          $preference->back_urls = $back_urls;

          // Configurações de pagamento
          $payment_methods = array({
          excluded_payment_methods: [
                    {
                              id: "amex"
                    },
                    {
                              id: "elo"
                    },
                    {
                              id: "hipercard"
                    },
                    {
                              id: "pec"
                    }
          ],
          excluded_payment_types: [
                    {
                              id: "debit_card"
                    }
          ],
          installments: 10
});
          $preference->payment_methods = $payment_methods;

          $preference->notification_url = 'https://www.your-site.com/ipn';
          $preference->statement_descriptor = 'MEUNEGOCIO';
          $preference->external_reference = 'Reference_1234';
          $preference->expires = true;
          $preference->expiration_date_from = '2016-02-01T12:00:00.000-04:00';
          $preference->expiration_date_to = '2016-02-28T12:00:00.000-04:00';

          $preference->save();
        ?>
        
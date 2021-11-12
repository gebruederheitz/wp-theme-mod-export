<?php /* Utility page for importing & exporting theme_mods */ ?>
<style>
    h1 {
        text-align: center;
        font-size: 2rem;
        font-variant: small-caps;
    }

    .columns {
        display: flex;
        flex-flow: row wrap;
        padding: 1rem;
        min-height: 480px;
        align-items: stretch;
        margin-right: 2em;
        background-color: rgba(0, 80, 120, .1);
    }

    @media screen and (min-width: 768px) {
        .columns {
            padding: 2rem;
        }
    }

    .columns .column {
        flex: 1 0 320px;
        padding: 1rem 2rem;
        display: flex;
        flex-flow: column;
    }

    .column:hover {
        background-color: rgba(0, 80, 120, .1);
    }

    .column:not(:last-child) {
        border-right: 1px solid #333;
    }

    @media screen and (min-width: 768px) {
        .column:not(:last-child) {
            border-right: 1px solid #333;
        }
    }

    .column h2 {
        text-align: center;
        font-size: 1.6rem;
        margin-bottom: 1em;
    }

    .columns .column .column-content {
        display: flex;
        flex: 1 1 100%;
        flex-flow: column;
        justify-content: center;
    }

    .columns .column .button-primary {
        margin-bottom: 1em;
    }

    .columns .column .button-secondary {
        text-align: center;
    }

    .columns .column input {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        text-align: center;
        margin-bottom: 1em;
    }
</style>


<h1>Customizer settings import / export</h1>

<div class="columns">
    <div class="column">
        <h2>Aktuelle Einstellungen exportieren</h2>
        <div class="column-content">
            <button type="button" class="button-primary" data-action="export">
                Export
            </button>
        </div>
    </div>

    <div class="column">
        <h2>Einstellungen importieren</h2>
        <div class="column-content">
            <form
                action="<?= wp_nonce_url('/wp-json/ghwp/v1/theme-mods/import', 'wp_rest') ?>"
                method="post"
                enctype="multipart/form-data"
                id="form-import"
            >
                <input type="file" accept="application/json" name="file" />
                <input type="submit" value="Hochladen" class="button-primary" />
            </form>
            <div class="notice--import"></div>
        </div>
    </div>
</div>


<script>
    const exportButton = document.querySelector('[data-action="export"]');
    exportButton.addEventListener('click', function() {
        window.fetch("<?= wp_nonce_url('/wp-json/ghwp/v1/theme-mods/export', 'wp_rest') ?>")
            .then(data => {
               console.log(data);
               return data.json();
            })
            .then(string => {
                const blob = new Blob(string, {type: 'application/json'});
                const a = document.createElement('A');
                a.download = 'theme-mods-export.json';
                a.href = window.URL.createObjectURL(blob);
                a.innerText = 'Datei speichern';
                a.classList.add('button-secondary');

                exportButton.insertAdjacentElement('afterend', a);
            })
        ;
    });

    const importNotice = document.querySelector('.notice--import');
    const importForm = document.querySelector('#form-import');
    importForm.addEventListener('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

      window.fetch(importForm.action, {
          method: importForm.method,
          <? /*
            with explicit headers, fetch does not set the boundary correctly
            // headers: {
            //     'content-type': 'multipart/form-data',
            // },
        */ ?>
        body: new FormData(importForm),
      }).then(res => {
          if (res.ok) {
              importNotice.classList.remove('notice-error');
              importNotice.classList.add('notice', 'notice-success');
              importNotice.innerText = 'Done.';
          } else {
              importNotice.classList.remove('notice-success');
              importNotice.classList.add('notice', 'notice-error');
              importNotice.innerText = 'Failed. Please check the file and try again.';
          }
      });
    });
</script>

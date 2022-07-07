<div class="container-fluid">
    <figure class="text-center">
        <blockquote class="blockquote">
            <h1>Generador de Crud </h1>
        </blockquote>
    </figure>


    <div class="form">

        <form action="index.php?page=ORM_generador" method="post">
            <label for="table"><input id="table" name="table" value="{{table}}" placeholder="table" type="text"></label>
            <label for="namespace"><input id="namespace" value="{{namespace}}" name="namespace" placeholder="namespace" type="text"></label>
            <label for="entity"><input id="entity" name="entity" value="{{entity}}" placeholder="entity" type="text"></label> &nbsp;&nbsp; 
            <button type="submit" class="btn btn-primary btn-lg">Generar</button>
        </form>
    </div>


    <br>
    <br>
    {{if DaoModel}}
    <figure class="text-center">
        <blockquote class="blockquote">
            <h3>Modelo de la Tabla: {{table}}</h3> &nbsp;
            <button type="button" onclick="copiarAlPortapapeles('p1')" class="btn btn-secondary">Copiar</button>

        </blockquote>
    </figure>
    <div class="card">

        <div class="card-body">
            <pre id="p1" class="chroma">{{DaoModel}}</pre>
        </div>
    </div>
    {{endif DaoModel}}

    <br>
    <br>

    {{if ControllerForList}}
    <figure class="text-center">
        <blockquote class="blockquote">
            <h3>Controlador de Listado:{{table}}</h3>
            &nbsp;&nbsp;  &nbsp;&nbsp;  
            <button type="button" onclick="copiarAlPortapapeles('p2')" class="btn btn-secondary">Copiar</button>
        </blockquote>
    </figure>
    <div class="card">

        <div class="card-body">
            <pre class="chroma" id="p2">{{ControllerForList}}</pre>
        </div>
    </div>
    {{endif ControllerForList}}

    <br>
    <br>


    {{if ControllerForTable}}
    <figure class="text-center">
        <blockquote class="blockquote">
            <h3>Controlador de la Tabla: {{table}}</h3>
            &nbsp;&nbsp;
            <button type="button" onclick="copiarAlPortapapeles('p3')" class="btn btn-secondary">Copiar</button>
        </blockquote>
    </figure>
    <div class="card">

        <div class="card-body">
            <pre class="chroma" id="p3">{{ControllerForTable}}</pre>
        </div>
    </div>
    {{endif ControllerForTable}}
    <br>
    <br>

</div>    

<script type="text/javascript">
    function copiarAlPortapapeles(id_elemento) {
        var content = document.getElementById(id_elemento).textContent;
        navigator.clipboard.writeText(content)
            .then(() => {
                alert("Texto copiado en el porta papeles...");
                console.log("Text copied to clipboard...")
            })
            .catch(err => {
                alert("Error al copiar en el porta papeles...");
                console.log('Something went wrong', err);
            })
    }
</script>
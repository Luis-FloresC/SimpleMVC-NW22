<h1>{{mode_desc}}</h1>
<section>
   <form action="index.php?page=mnt_persona" method="post">
        <input type="hidden" name="mode" value="{{mode}}" />
        <input type="hidden" name="crsf_token" value="{{crsf_token}}" />
        <input type="hidden" name="id" value="{{id}}" />
 <fieldset>
                <label for="identidad">identidad</label>
                <input {{if readonly}}readonly{{endif readonly}} type="text" id="identidad" name="identidad" placeholder="identidad" value="{{identidad}}"/>
{{if error_identidad}}
                     {{foreach error_identidad}}
                       <div class="error">{{this}}</div>
                     {{endfor error_identidad}}
                 {{endif error_identidad}}
</fieldset>
 <fieldset>
                <label for="nombre">nombre</label>
                <input {{if readonly}}readonly{{endif readonly}} type="text" id="nombre" name="nombre" placeholder="nombre" value="{{nombre}}"/>
{{if error_nombre}}
                     {{foreach error_nombre}}
                       <div class="error">{{this}}</div>
                     {{endfor error_nombre}}
                 {{endif error_nombre}}
</fieldset>
 <fieldset>
                <label for="edad">edad</label>
                <input {{if readonly}}readonly{{endif readonly}} type="text" id="edad" name="edad" placeholder="edad" value="{{edad}}"/>
</fieldset>
 <fieldset>
        {{if showBtn}}
          <button type="submit" name="btnEnviar">{{btnEnviarText}}</button>
          &nbsp;
        {{endif showBtn}}
        <button name="btnCancelar" id="btnCancelar">Cancelar</button>
      </fieldset>
   </form>
</section>
<script>
        document.addEventListener("DOMContentLoaded", function(){
          document.getElementById("btnCancelar").addEventListener("click", function(e){
            e.preventDefault();
            e.stopPropagation();
            window.location.href = "index.php?page=mnt_personas";
          });
        });
      </script>
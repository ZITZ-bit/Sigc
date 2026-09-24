Schema::create('pacientes', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('apellido');
    $table->string('correo')->unique();
    $table->date('fecha_nacimiento');
    $table->timestamps();
});

describe('Registro y redirección', () => {
  it('Registrar usuario y verificar en pantalla principal', () => {
    cy.visit('http://localhost/');
		cy.viewport(1380, 720);
    cy.get('#register').click();
    cy.get('#selectEntidad').select('Cliente');
    cy.get('#name').type('Alvaro');
    cy.get('#email').type('Alvarocorreo12@gmail.com');
    cy.get('#password').type('Alvaro1234');
    cy.get('#dni').type('49450649X');
    cy.get('#foto_dni').attachFile('foto.jpg');
    cy.contains('Crear Cliente').click();
    cy.get('#foto-flamenco').click();
    cy.get('#comprar').click();
    cy.get('#carrito').click();
    cy.contains('Ver Carrito').click();
    cy.get('#pagar').click();
  });
});

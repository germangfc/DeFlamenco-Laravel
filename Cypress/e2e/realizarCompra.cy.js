describe('Registro y redirección', () => {
  it('Registrar usuario y verificar en pantalla principal', () => {
    cy.visit('http://localhost/');

    cy.get('#menu-hamburguer').click();
    cy.contains('Registrarse').click();
    cy.get('#selectEntidad').select('Cliente');
    cy.get('#name').type('Alvaro');
    cy.get('#email').type('Alvarocorreo@gmail.com');
    cy.get('#password').type('Alvaro1234');
    cy.get('#dni').type('49450649M');
    cy.get('#foto_dni').attachFile('foto.jpg');
    cy.contains('Crear Cliente').click();
    cy.get('#foto-flamenco').click();
    cy.get('#comprar').click();
    cy.get('.h-5 w-5').click();

  });
});

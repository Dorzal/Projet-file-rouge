﻿using Commande.Model;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Mail;
using Mailjet.Client;
using Mailjet.Client.Resources;
using Newtonsoft.Json.Linq;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;

namespace Commande.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class CommandeController : ControllerBase
    {
        static HttpClient client = new HttpClient();
        private readonly CommandeContext _context;

        public CommandeController(CommandeContext context)
        {
            _context = context;
        }

        [HttpGet]
        public List<Model.Commande> Get()
        {
            return _context.Commande.Include(x => x.Articles).ToList();
        }

        [HttpGet("{id}", Name = "id")]
        public Model.Commande GetCommandeById(int id)
        {
            return _context.Commande.Where(x => x.Id == id).FirstOrDefault();
        }

        [HttpPost]
        public async Task<Model.Commande> CreateCommandeAsync([FromBody]Model.Commande commande)
        {
            HttpResponseMessage stock = await client.PostAsJsonAsync(
                                        "http://mc-stock/api/stock/commande", commande.Articles);
            if(stock.StatusCode == HttpStatusCode.OK)
            {
                _context.Commande.Add(commande);
                            _context.SaveChanges();
                            HttpResponseMessage response = await client.PostAsJsonAsync(
                                            "http://mc-mail/api/mail", commande);
                            return commande;
            }else {
                return null;
            }


        }

        [HttpPut]
        public void EditCommande([FromBody]Model.Commande commande)
        {
            _context.Entry(commande).State = EntityState.Modified;
            _context.SaveChanges();
        }

        [HttpDelete]
        public void DeleteCommande(int id)
        {
            Model.Commande commandeToDelete = _context.Commande
                .Where(x => x.Id == id)
                .FirstOrDefault();

            _context.Commande.Remove(commandeToDelete);
            _context.SaveChanges();
        }
    }
}
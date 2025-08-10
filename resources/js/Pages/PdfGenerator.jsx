import React from 'react';
import { Document, Page, Text, View, PDFViewer, StyleSheet } from '@react-pdf/renderer';
import { usePage } from '@inertiajs/react';

const PdfGenerator = () => {
  const { props } = usePage();
  const { type, document, user, madinia } = props;

  // Helpers
  const formatMoney = (amount) => {
    if (!amount && amount !== 0) return '0,00 €';
    const num = parseFloat(amount);
    const formatted = num.toFixed(2).replace('.', ',');
    const parts = formatted.split(',');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return parts.join(',') + ' €';
  };

  const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
  };

  const humanize = (value) => {
    if (!value) return '-';
    return String(value)
      .replace(/_/g, ' ')
      .toLowerCase()
      .replace(/(^|\s)\S/g, (t) => t.toUpperCase());
  };

  const formatUnite = (unite, quantite) => {
    const map = { heure: 'heure', jour: 'jour', semaine: 'semaine', mois: 'mois', unite: 'unité', forfait: 'forfait', licence: 'licence' };
    const base = map[unite] || unite || '';
    if (!base) return '';
    if (['mois', 'forfait'].includes(base)) return `${base}`;
    const plural = quantite > 1 ? `${base}s` : base;
    return plural.replace('unites', 'unités').replace('moiss', 'mois');
  };

  // Styles minimalistes et professionnels
  const styles = StyleSheet.create({
    page: { flexDirection: 'column', backgroundColor: '#ffffff', padding: 0, fontSize: 11, fontFamily: 'Helvetica', color: '#0F172A' },
    header: { paddingVertical: 18, paddingHorizontal: 28, borderBottom: '1px solid #E5E7EB' },
    headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-end' },
    brand: { fontSize: 18, fontWeight: 'bold', letterSpacing: 0.5 },
    meta: { textAlign: 'right' },
    metaTitle: { fontSize: 14, fontWeight: 'bold' },
    metaSub: { fontSize: 10, color: '#475569', marginTop: 2 },
    content: { padding: 28, flex: 1 },
    sectionTitle: { fontSize: 12, fontWeight: 'bold', textTransform: 'uppercase', color: '#111827', paddingBottom: 6, borderBottom: '1px solid #E5E7EB', marginBottom: 10 },
    twoCols: { flexDirection: 'row', gap: 18, marginBottom: 18 },
    col: { width: '50%' },
    infoRow: { flexDirection: 'row', marginBottom: 4 },
    label: { width: '38%', fontWeight: 'bold', color: '#374151' },
    value: { width: '62%' },
    table: { border: '1px solid #E5E7EB', borderRadius: 4, overflow: 'hidden', marginTop: 10, marginBottom: 18 },
    tableHeader: { flexDirection: 'row', backgroundColor: '#F3F4F6', paddingVertical: 10, paddingHorizontal: 12, borderBottom: '1px solid #E5E7EB' },
    th: { fontSize: 10, fontWeight: 'bold', color: '#111827', textTransform: 'uppercase' },
    row: { flexDirection: 'row', paddingVertical: 9, paddingHorizontal: 12, borderBottom: '1px solid #F1F5F9' },
    cellService: { width: '40%', fontSize: 10 },
    cellQty: { width: '12%', fontSize: 10, textAlign: 'center', color: '#475569' },
    cellPrice: { width: '16%', fontSize: 10, textAlign: 'right' },
    cellDiscount: { width: '12%', fontSize: 10, textAlign: 'right', color: '#475569' },
    cellVat: { width: '8%', fontSize: 10, textAlign: 'right', color: '#475569' },
    cellTotal: { width: '12%', fontSize: 10, textAlign: 'right', fontWeight: 'bold' },
    serviceName: { fontWeight: 'bold', marginBottom: 2 },
    serviceDesc: { fontSize: 9, color: '#6B7280' },
    totalsBox: { marginLeft: '50%', width: '50%', border: '1px solid #E5E7EB', borderRadius: 4, padding: 12, backgroundColor: '#FAFAFA' },
    totalRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 6 },
    grandRow: { flexDirection: 'row', justifyContent: 'space-between', borderTop: '1px solid #E5E7EB', paddingTop: 8, marginTop: 6 },
    grandLabel: { fontSize: 12, fontWeight: 'bold' },
    grandValue: { fontSize: 13, fontWeight: 'bold' },
    footer: { paddingVertical: 12, paddingHorizontal: 28, borderTop: '1px solid #E5E7EB', color: '#6B7280', fontSize: 9 },
  });

  // Pagination des lignes
  const LINES_PER_PAGE = 8; // seuil de lignes par page
  const allLines = Array.isArray(document?.lignes) ? document.lignes : [];
  const pages = [];
  for (let i = 0; i < allLines.length; i += LINES_PER_PAGE) {
    pages.push(allLines.slice(i, i + LINES_PER_PAGE));
  }
  if (pages.length === 0) pages.push([]);

  const Header = () => (
    <View style={styles.header}>
      <View style={styles.headerRow}>
        <Text style={styles.brand}>{(madinia?.name || 'Madin.IA').toUpperCase()}</Text>
        <View style={styles.meta}>
          <Text style={styles.metaTitle}>
            {type === 'devis' ? 'Devis' : 'Facture'} {type === 'devis' ? document.numero_devis : document.numero_facture}
          </Text>
          <Text style={styles.metaSub}>Date: {formatDate(document.date_devis || document.date_facture)}</Text>
        </View>
      </View>
    </View>
  );

  const IdentityBlocks = () => (
    <View style={styles.twoCols}>
      <View style={styles.col}>
        <Text style={styles.sectionTitle}>Émetteur</Text>
        <View style={styles.infoRow}><Text style={styles.label}>Entreprise</Text><Text style={styles.value}>{madinia?.name || 'Madin.IA'}</Text></View>
        <View style={styles.infoRow}><Text style={styles.label}>Contact</Text><Text style={styles.value}>{user?.name || 'Administration'}</Text></View>
        <View style={styles.infoRow}><Text style={styles.label}>Email</Text><Text style={styles.value}>{user?.email || madinia?.email || 'contact@madinia.fr'}</Text></View>
        {madinia?.telephone && (<View style={styles.infoRow}><Text style={styles.label}>Téléphone</Text><Text style={styles.value}>{madinia.telephone}</Text></View>)}
        {madinia?.adresse && (<View style={styles.infoRow}><Text style={styles.label}>Adresse</Text><Text style={styles.value}>{madinia.adresse}</Text></View>)}
        {madinia?.siret && (<View style={styles.infoRow}><Text style={styles.label}>SIRET</Text><Text style={styles.value}>{madinia.siret}</Text></View>)}
      </View>

      <View style={styles.col}>
        <Text style={styles.sectionTitle}>Client</Text>
        <View style={styles.infoRow}><Text style={styles.label}>Nom</Text><Text style={styles.value}>{document.client?.nom || document.client?.raison_sociale}</Text></View>
        {document.client?.entreprise && (<View style={styles.infoRow}><Text style={styles.label}>Entreprise</Text><Text style={styles.value}>{document.client.entreprise.nom}</Text></View>)}
        {document.client?.adresse && (<View style={styles.infoRow}><Text style={styles.label}>Adresse</Text><Text style={styles.value}>{document.client.adresse}</Text></View>)}
        {document.client?.ville && (<View style={styles.infoRow}><Text style={styles.label}>Ville</Text><Text style={styles.value}>{document.client.code_postal} {document.client.ville}</Text></View>)}
      </View>
    </View>
  );

  const LinesTable = ({ rows }) => (
    <View style={styles.table}>
      <View style={styles.tableHeader}>
        <Text style={[styles.th, styles.cellService]}>Service / Description</Text>
        <Text style={[styles.th, styles.cellQty]}>Qté</Text>
        <Text style={[styles.th, styles.cellPrice]}>Prix HT</Text>
        <Text style={[styles.th, styles.cellDiscount]}>Remise</Text>
        <Text style={[styles.th, styles.cellVat]}>TVA</Text>
        <Text style={[styles.th, styles.cellTotal]}>Total HT</Text>
      </View>
      {rows.map((ligne, index) => (
        <View key={index} style={styles.row}>
          <View style={styles.cellService}>
            <Text style={styles.serviceName}>{ligne.service?.nom || 'Service non spécifié'}</Text>
            {ligne.description_personnalisee && (<Text style={styles.serviceDesc}>{ligne.description_personnalisee}</Text>)}
            {ligne.remise_pourcentage > 0 && (
              <Text style={{ fontSize: 9, color: '#6B7280' }}>
                Remise appliquée: {ligne.remise_pourcentage}% sur {formatMoney(ligne.prix_unitaire_ht)}
              </Text>
            )}
          </View>
          <Text style={styles.cellQty}>{ligne.quantite} {formatUnite(ligne.unite, ligne.quantite)}</Text>
          <Text style={styles.cellPrice}>{formatMoney(ligne.prix_unitaire_ht)}</Text>
          <Text style={styles.cellDiscount}>{(ligne.remise_pourcentage ?? 0).toFixed ? `${Number(ligne.remise_pourcentage).toFixed(0)}%` : `${ligne.remise_pourcentage || 0}%`}</Text>
          <Text style={styles.cellVat}>{(ligne.taux_tva ?? 0).toFixed ? `${Number(ligne.taux_tva).toFixed(0)}%` : `${ligne.taux_tva || 0}%`}</Text>
          <Text style={styles.cellTotal}>{formatMoney(ligne.montant_ht)}</Text>
        </View>
      ))}
    </View>
  );

  const Totals = () => (
    <View style={styles.totalsBox}>
      <View style={styles.totalRow}><Text>Sous-total HT</Text><Text>{formatMoney(document.montant_ht)}</Text></View>
      <View style={styles.totalRow}><Text>TVA ({document.taux_tva}%)</Text><Text>{formatMoney(document.montant_tva)}</Text></View>
      <View style={styles.grandRow}><Text style={styles.grandLabel}>Total TTC</Text><Text style={styles.grandValue}>{formatMoney(document.montant_ttc)}</Text></View>
    </View>
  );

  const Footer = () => (
    <View style={styles.footer}>
      <Text>{(madinia?.name || 'Madin.IA')} — {madinia?.adresse || '1 Chemin du Sud, 97233 Schoelcher'} • SIRET {madinia?.siret || '934 303 843 00015'} • {madinia?.email || 'contact@madinia.fr'}</Text>
    </View>
  );

  const DocumentPDF = () => (
    <Document>
      {pages.map((rows, idx) => (
        <Page key={idx} size="A4" style={styles.page}>
          <Header />
          <View style={styles.content}>
            {idx === 0 && <IdentityBlocks />}
            <LinesTable rows={rows} />
            {idx === pages.length - 1 && <Totals />}
          </View>
          <Footer />
        </Page>
      ))}
    </Document>
  );

  return (
    <div className="w-full h-screen bg-white">
      <PDFViewer className="w-full h-full border-0">
        <DocumentPDF />
      </PDFViewer>
    </div>
  );
};

export default PdfGenerator;

import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Image, SafeAreaView, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { FeedStackParamList, Viaje } from '../navigation/types';
import { tripService } from '../services/trip.service';

type Props = NativeStackScreenProps<FeedStackParamList, 'FeedList'>;

export default function FeedScreen({ navigation }: Props) {
  const [viajes, setViajes] = useState<Viaje[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchViajes();
  }, []);

  const fetchViajes = async () => {
    try {
      const data = await tripService.listarViajes();
      setViajes(data);
    } catch (error) {
      console.error('Error fetching viajes', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.safeArea}>
        <View style={[styles.container, { justifyContent: 'center', alignItems: 'center' }]}>
          <ActivityIndicator size="large" color="#FBBF24" />
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Viajes Disponibles</Text>
          <Text style={styles.headerSubtitle}>Encuentra tu próximo viaje</Text>
        </View>

        <FlatList
          data={viajes}
          keyExtractor={item => String(item.id)}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.listContainer}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={styles.card}
              activeOpacity={0.8}
              onPress={() => navigation.navigate('FeedDetail', item)}
            >
              <Image source={{ uri: item.imageUrl || 'https://images.unsplash.com/photo-1544463878-a3f29bb5ebfb?q=80&w=600&auto=format&fit=crop' }} style={styles.cardImage} />
              <View style={styles.cardOverlay}>
                <View style={styles.ratingBadge}>
                  <Text style={styles.ratingText}>★ {item.conductor?.calificacion || '5.0'}</Text>
                </View>
              </View>
              <View style={styles.cardContent}>
                <View style={styles.titleRow}>
                  <Text style={styles.cardTitle}>{item.destino}</Text>
                  <Text style={styles.cardPrice}>${item.costo_por_asiento}</Text>
                </View>
                <Text style={styles.cardLocation}>🚗 Conductor: {item.conductor?.nombre || 'Desconocido'} - {item.vehiculo?.marca || 'Auto'}</Text>
              </View>
            </TouchableOpacity>
          )}
        />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#09090B',
  },
  container: {
    flex: 1,
  },
  header: {
    paddingHorizontal: 24,
    paddingTop: 16,
    paddingBottom: 24,
  },
  headerTitle: {
    fontSize: 32,
    fontWeight: '800',
    color: '#FFFFFF',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#A1A1AA',
  },
  listContainer: {
    paddingHorizontal: 16,
    paddingBottom: 24,
  },
  card: {
    backgroundColor: '#18181B',
    borderRadius: 24,
    marginBottom: 24,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#27272A',
  },
  cardImage: {
    width: '100%',
    height: 180,
  },
  cardOverlay: {
    position: 'absolute',
    top: 16,
    right: 16,
  },
  ratingBadge: {
    backgroundColor: 'rgba(9, 9, 11, 0.7)',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
  },
  ratingText: {
    color: '#FBBF24',
    fontWeight: 'bold',
    fontSize: 14,
  },
  cardContent: {
    padding: 20,
  },
  titleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#FFFFFF',
    flex: 1,
    paddingRight: 16,
  },
  cardPrice: {
    fontSize: 18,
    fontWeight: '800',
    color: '#FBBF24',
  },
  cardLocation: {
    fontSize: 14,
    color: '#A1A1AA',
    fontWeight: '500',
  }
});
